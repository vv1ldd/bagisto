const hre = require("hardhat");

async function main() {
  const [deployer] = await hre.ethers.getSigners();
  const adminAddress = process.env.ADMIN_ADDRESS;
  const minterAddress = process.env.HOT_WALLET_ADDRESS;

  console.log("Deploying MeanlyGiftNFT with account:", deployer.address);
  
  const MeanlyGiftNFT = await hre.ethers.getContractFactory("MeanlyGiftNFT");
  const nft = await MeanlyGiftNFT.deploy(adminAddress, minterAddress);
  
  await nft.waitForDeployment();
  
  console.log("MeanlyGiftNFT deployed to:", await nft.getAddress());
}

main().catch((error) => {
  console.error(error);
  process.exitCode = 1;
});
