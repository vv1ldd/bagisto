// SPDX-License-Identifier: MIT
pragma solidity ^0.8.20;

import "@openzeppelin/contracts/token/ERC721/ERC721.sol";
import "@openzeppelin/contracts/token/ERC721/extensions/ERC721URIStorage.sol";
import "@openzeppelin/contracts/access/AccessControl.sol";
import "@openzeppelin/contracts/utils/Pausable.sol";

/**
 * @title MeanlyGiftNFT
 * @dev ERC721 Token for Purchase Gifts with production safety features.
 */
contract MeanlyGiftNFT is ERC721, ERC721URIStorage, AccessControl, Pausable {
    bytes32 public constant MINTER_ROLE = keccak256("MINTER_ROLE");
    
    uint256 private _nextTokenId;

    /**
     * @dev Constructor
     * @param defaultAdmin Address of the Cold Wallet (Owner)
     * @param minter Address of the Backend Hot Wallet (Minter)
     */
    constructor(address defaultAdmin, address minter) ERC721("Meanly Gift", "MFT") {
        _grantRole(DEFAULT_ADMIN_ROLE, defaultAdmin);
        _grantRole(MINTER_ROLE, minter);
        
        // Start token IDs from 1
        _nextTokenId = 1;
    }

    /**
     * @dev Mints a new NFT with metadata URI. Restricted to MINTER_ROLE and only when not paused.
     */
    function safeMint(address to, string memory uri) public onlyRole(MINTER_ROLE) whenNotPaused {
        uint256 tokenId = _nextTokenId++;
        _safeMint(to, tokenId);
        _setTokenURI(tokenId, uri);
    }

    /**
     * @dev Pauses all minting and transfers. Emergency "Panic Button".
     */
    function pause() public onlyRole(DEFAULT_ADMIN_ROLE) {
        _pause();
    }

    /**
     * @dev Unpauses the contract.
     */
    function unpause() public onlyRole(DEFAULT_ADMIN_ROLE) {
        _unpause();
    }

    /**
     * @dev Hook that is called before any token transfer.
     */
    function _update(address to, uint256 tokenId, address auth) internal override(ERC721) whenNotPaused returns (address) {
        return super._update(to, tokenId, auth);
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
