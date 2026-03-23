// SPDX-License-Identifier: MIT
pragma solidity ^0.8.20;

import "@openzeppelin/contracts/token/ERC721/ERC721.sol";
import "@openzeppelin/contracts/token/ERC721/extensions/ERC721URIStorage.sol";
import "@openzeppelin/contracts/access/AccessControl.sol";

/**
 * @title MeanlyGiftNFT
 * @dev ERC721 Non-Fungible Token for Purchase Gifts.
 * The Hot Wallet (Minter) can mint new NFTs to users.
 * The Cold Wallet (Admin) manages the minter roles.
 */
contract MeanlyGiftNFT is ERC721, ERC721URIStorage, AccessControl {
    bytes32 public constant MINTER_ROLE = keccak256("MINTER_ROLE");
    uint256 private _nextTokenId;

    /**
     * @dev Constructor
     * @param defaultAdmin Address of the Cold Wallet (Owner)
     * @param minter Address of the Backend Hot Wallet (Minter)
     */
    constructor(address defaultAdmin, address minter) ERC721("Meanly Gift", "MGIFT") {
        _grantRole(DEFAULT_ADMIN_ROLE, defaultAdmin);
        _grantRole(MINTER_ROLE, minter);
        
        // Start token IDs from 1
        _nextTokenId = 1;
    }

    /**
     * @dev Mints a new NFT with a specific metadata URI to a buyer's address.
     * Can only be called by an account with the MINTER_ROLE.
     */
    function safeMint(address to, string memory uri) public onlyRole(MINTER_ROLE) {
        uint256 tokenId = _nextTokenId++;
        _safeMint(to, tokenId);
        _setTokenURI(tokenId, uri);
    }

    // The following functions are overrides required by Solidity for multiple inheritance.
    function tokenURI(uint256 tokenId)
        public
        view
        override(ERC721, ERC721URIStorage)
        returns (string memory)
    {
        return super.tokenURI(tokenId);
    }

    function supportsInterface(bytes4 interfaceId)
        public
        view
        override(ERC721, ERC721URIStorage, AccessControl)
        returns (bool)
    {
        return super.supportsInterface(interfaceId);
    }
}
