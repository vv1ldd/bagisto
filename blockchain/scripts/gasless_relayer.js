const { ethers } = require("ethers");
require("dotenv").config({ path: __dirname + '/../.env' });

async function main() {
    try {
        // Read JSON payload from stdin
        let inputData = '';
        for await (const chunk of process.stdin) {
            inputData += chunk;
        }
        
        const payload = JSON.parse(inputData);
        
        const {
            customerPrivateKey,
            targetAddress,
            amountStr, // String representing ether, e.g. "80"
            tokenAddress,
            hotWalletPrivateKey,
            rpcUrl
        } = payload;

        // 1. Setup Providers and Wallets
        const provider = new ethers.JsonRpcProvider(rpcUrl);
        const hotWallet = new ethers.Wallet(hotWalletPrivateKey, provider);
        const customerWallet = new ethers.Wallet(customerPrivateKey, provider);

        // 2. Token Contract Instantiation
        const abi = [
            "function permitAndTransfer(address owner, address to, uint256 value, uint256 deadline, uint8 v, bytes32 r, bytes32 s) external",
            "function nonces(address owner) view external returns (uint256)",
            "function name() view external returns (string)",
            "function version() view external returns (string)",
            "function paused() view external returns (bool)",
            "function balanceOf(address owner) view external returns (uint256)"
        ];
        
        const tokenContract = new ethers.Contract(tokenAddress, abi, hotWallet);
        
        // 3. Pre-flight Checks & Data Gathering
        const [tokenName, isPaused, hotWalletEth, customerTokens, nonce] = await Promise.all([
            tokenContract.name(),
            tokenContract.paused(),
            provider.getBalance(hotWallet.address),
            tokenContract.balanceOf(customerWallet.address),
            tokenContract.nonces(customerWallet.address)
        ]);

        const value = ethers.parseEther(amountStr.toString());
        const deadline = BigInt(Math.floor(Date.now() / 1000) + 3600);
        const network = await provider.getNetwork();
        const chainId = Number(network.chainId);

        // Debug Log (will be captured in stderr/stdout by PHP)
        console.warn(JSON.stringify({
            debug: "Pre-flight State",
            network: { chainId, name: network.name },
            contract: { address: tokenAddress, name: tokenName, paused: isPaused },
            hotWallet: { address: hotWallet.address, eth: ethers.formatEther(hotWalletEth) },
            customer: { 
                db_address: targetAddress, // Address where we expect tokens (stored in DB as credits_id)
                derived_address: customerWallet.address, // Address derived from the private key in DB
                tokens: ethers.formatEther(customerTokens), 
                nonce: nonce.toString() 
            },
            transaction: { target: targetAddress, amount: amountStr, amountWei: value.toString() }
        }));

        if (isPaused) throw new Error("Contract is currently Paused (Security Lock).");
        if (customerTokens < value) throw new Error(`Insufficient tokens on-chain. Have ${ethers.formatEther(customerTokens)}, need ${amountStr}`);
        if (hotWalletEth < ethers.parseEther("0.00001")) throw new Error("Hot Wallet has insufficient ETH for gas.");

        // 4. EIP-712 Typed Data Specification
        const domain = {
            name: tokenName,
            version: "1",
            chainId: chainId,
            verifyingContract: tokenAddress
        };

        const types = {
            Permit: [
                { name: "owner", type: "address" },
                { name: "spender", type: "address" },
                { name: "value", type: "uint256" },
                { name: "nonce", type: "uint256" },
                { name: "deadline", type: "uint256" }
            ]
        };

        const message = {
            owner: customerWallet.address,
            spender: hotWallet.address,
            value: value,
            nonce: nonce,
            deadline: deadline
        };

        // 5. Sign Typed Data (Offline)
        const signature = await customerWallet.signTypedData(domain, types, message);
        const sig = ethers.Signature.from(signature);

        // 6. Execute Gasless Relay (Hot Wallet pays gas)
        // We use populateTransaction and estimateGas to get better error messages if it fails
        try {
            const tx = await tokenContract.permitAndTransfer(
                customerWallet.address,
                targetAddress,
                value,
                deadline,
                sig.v,
                sig.r,
                sig.s
            );

            // Return immediately after broadcast for speed & resilience
            // Confirmation will be handled by a background job
            console.log(JSON.stringify({
                success: true,
                tx_hash: tx.hash,
                message: "Gasless transaction broadcasted successfully."
            }));
        } catch (txError) {
             // Handle Revert Data directly if possible
             let reason = txError.message;
             if (txError.data) reason += ` (Data: ${txError.data})`;
             throw new Error(reason);
        }

    } catch (error) {
        console.error(JSON.stringify({
            success: false,
            error: error.message,
            stack: error.stack
        }));
        process.exit(1);
    }
}

main();
