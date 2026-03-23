// SPDX-License-Identifier: MIT
pragma solidity ^0.8.20;

import "@openzeppelin/contracts/token/ERC721/ERC721.sol";
import "@openzeppelin/contracts/token/ERC721/extensions/ERC721URIStorage.sol";
import "@openzeppelin/contracts/token/ERC721/extensions/ERC721Burnable.sol";
import "@openzeppelin/contracts/access/AccessControl.sol";
import "@openzeppelin/contracts/utils/Pausable.sol";

/**
 * @title MeanlyGiftNFT
 * @dev ERC721 Token for Purchase Gifts with production safety and burn features.
 */
contract MeanlyGiftNFT is ERC721URIStorage, ERC721Burnable, AccessControl, Pausable {
    bytes32 public constant MINTER_ROLE = keccak256("MINTER_ROLE");
    
    uint256 private _nextTokenId;
    
    // Safety limit for batch operations and mass emission
    uint256 public maxMintPerTx = 50;

    // Gas safety limit for batch operations
    uint256 public constant MAX_BATCH_SIZE = 200;

    // Address of the project treasury for economic operations
    address public treasury;

    // Production Events for analytics and transparency
    event GiftMinted(address indexed to, uint256 indexed tokenId, string uri, string reason);
    event BurnedWithReason(address indexed from, uint256 indexed tokenId, string reason);
    event MinterAdded(address indexed account);
    event MinterRemoved(address indexed account);
    event MaxMintPerTxUpdated(uint256 oldLimit, uint256 newLimit);
    event BatchMint(uint256 recipientsCount);
    event TreasuryUpdated(address indexed oldTreasury, address indexed newTreasury);

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
     * @dev Mints a new NFT with metadata URI and audit reason. Restricted to MINTER_ROLE.
     */
    function safeMint(address to, string memory uri, string memory reason) public onlyRole(MINTER_ROLE) whenNotPaused {
        require(to != address(0), "Meanly: mint to the zero address");
        
        uint256 tokenId = _nextTokenId++;
        _safeMint(to, tokenId);
        _setTokenURI(tokenId, uri);
        
        emit GiftMinted(to, tokenId, uri, reason);
    }

    /**
     * @dev Gas-efficient batch minting for multiple recipients/URIs with audit reason.
     */
    function batchMint(address[] calldata recipients, string[] calldata uris, string memory reason) 
        external 
        onlyRole(MINTER_ROLE) 
        whenNotPaused 
    {
        require(recipients.length == uris.length, "Meanly: length mismatch");
        require(recipients.length <= MAX_BATCH_SIZE, "Meanly: batch too large");

        for (uint256 i = 0; i < recipients.length; i++) {
            require(recipients[i] != address(0), "Meanly: mint to the zero address");
            
            uint256 tokenId = _nextTokenId++;
            _safeMint(recipients[i], tokenId);
            _setTokenURI(tokenId, uris[i]);
            
            emit GiftMinted(recipients[i], tokenId, uris[i], reason);
        }

        emit BatchMint(recipients.length);
    }

    /**
     * @dev Destroys an NFT with an audit reason.
     */
    function burnWithReason(uint256 tokenId, string memory reason) public whenNotPaused {
        _burn(tokenId);
        emit BurnedWithReason(msg.sender, tokenId, reason);
    }

    /**
     * @dev Updates the treasury address. Only Admin can call this.
     */
    function setTreasury(address newTreasury) external onlyRole(DEFAULT_ADMIN_ROLE) {
        require(newTreasury != address(0), "Meanly: treasury is zero address");
        emit TreasuryUpdated(treasury, newTreasury);
        treasury = newTreasury;
    }

    /**
     * @dev Updates the safety limit. Only Admin (Cold Wallet) can call this.
     */
    function setMaxMintPerTx(uint256 newLimit) public onlyRole(DEFAULT_ADMIN_ROLE) {
        require(newLimit > 0, "Meanly: limit must be > 0");
        emit MaxMintPerTxUpdated(maxMintPerTx, newLimit);
        maxMintPerTx = newLimit;
    }

    /**
     * @dev Explicitly adds a new minter. Only Admin (Cold Wallet) can call this.
     */
    function addMinter(address account) external onlyRole(DEFAULT_ADMIN_ROLE) {
        require(account != address(0), "Meanly: minter is the zero address");
        grantRole(MINTER_ROLE, account);
        emit MinterAdded(account);
    }

    /**
     * @dev Explicitly removes a minter. Only Admin (Cold Wallet) can call this.
     */
    function removeMinter(address account) external onlyRole(DEFAULT_ADMIN_ROLE) {
        require(account != msg.sender, "Meanly: cannot remove yourself");
        revokeRole(MINTER_ROLE, account);
        emit MinterRemoved(account);
    }

    /**
     * @dev Simple helper to check if an account is a minter.
     */
    function isMinter(address account) external view returns (bool) {
        return hasRole(MINTER_ROLE, account);
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
