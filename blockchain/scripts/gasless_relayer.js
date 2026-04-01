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
            "function version() view external returns (string)"
        ];
        
        const tokenContract = new ethers.Contract(tokenAddress, abi, hotWallet);
        const tokenName = await tokenContract.name();
        const value = ethers.parseEther(amountStr.toString());

        // 3. EIP-2612 Permit Prerequisites
        const nonce = await tokenContract.nonces(customerWallet.address);
        const deadline = BigInt(Math.floor(Date.now() / 1000) + 3600); // 1 hour from now

        // Get network config dynamically if not provided
        const network = await provider.getNetwork();
        const chainId = Number(network.chainId);

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

        // Note: The spender is the hotWallet who will be executing the permitAndTransfer
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
        // permitAndTransfer(address owner, address to, uint256 value, uint256 deadline, uint8 v, bytes32 r, bytes32 s)
        const tx = await tokenContract.permitAndTransfer(
            customerWallet.address,
            targetAddress,
            value,
            deadline,
            sig.v,
            sig.r,
            sig.s
        );

        // Wait for 1 confirmation
        const receipt = await tx.wait(1);

        console.log(JSON.stringify({
            success: true,
            tx_hash: receipt.hash,
            message: "Gasless transaction relayed successfully."
        }));

    } catch (error) {
        console.error(JSON.stringify({
            success: false,
            error: error.message
        }));
        process.exit(1);
    }
}

main();
