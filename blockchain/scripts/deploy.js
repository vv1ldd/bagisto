const hre = require("hardhat");

async function main() {
  const [deployer] = await hre.ethers.getSigners();
  console.log("Deploying contracts with the account:", deployer.address);

  // Minter address should be the backend hot wallet (can be overridden via ENV)
  const MINTER_ADDRESS = process.env.MINTER_ADDRESS || deployer.address;
  // Admin address should be the cold wallet (can be overridden via ENV)
  const ADMIN_ADDRESS = process.env.ADMIN_ADDRESS || deployer.address;

  console.log("Configured Admin (Cold Wallet):", ADMIN_ADDRESS);
  console.log("Configured Minter (Hot Wallet):", MINTER_ADDRESS);

  // 1. Deploy MeanlyCoin (ERC20 Cashback)
  console.log("Deploying MeanlyCoin...");
  const MeanlyCoin = await hre.ethers.getContractFactory("MeanlyCoin");
  const coin = await MeanlyCoin.deploy(ADMIN_ADDRESS, MINTER_ADDRESS);
  await coin.waitForDeployment();
  console.log(`MeanlyCoin deployed to: ${await coin.getAddress()}`);

  // Wait 10 seconds to avoid "nonce too low" errors on Arbitrum
  console.log("Waiting 10 seconds for network synchronization...");
  await new Promise(resolve => setTimeout(resolve, 10000));

  // 2. Deploy MeanlyGiftNFT (ERC721 Gift)
  console.log("Deploying MeanlyGiftNFT...");
  const MeanlyGiftNFT = await hre.ethers.getContractFactory("MeanlyGiftNFT");
  const nft = await MeanlyGiftNFT.deploy(ADMIN_ADDRESS, MINTER_ADDRESS);
  await nft.waitForDeployment();
  console.log(`MeanlyGiftNFT deployed to: ${await nft.getAddress()}`);

  console.log("Deployment complete.");
}

main().catch((error) => {
  console.error(error);
  process.exitCode = 1;
});
