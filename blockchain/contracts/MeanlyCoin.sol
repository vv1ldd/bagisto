// SPDX-License-Identifier: MIT
pragma solidity ^0.8.20;

import "@openzeppelin/contracts/token/ERC20/ERC20.sol";
import "@openzeppelin/contracts/token/ERC20/extensions/ERC20Burnable.sol";
import "@openzeppelin/contracts/access/AccessControl.sol";
import "@openzeppelin/contracts/utils/Pausable.sol";

/**
 * @title MeanlyCoin
 * @dev ERC20 Token for Cashback system with production safety and burn features.
 */
contract MeanlyCoin is ERC20, ERC20Burnable, AccessControl, Pausable {
    bytes32 public constant MINTER_ROLE = keccak256("MINTER_ROLE");
    
    // Safety limit to prevent accidental or malicious mass emission
    uint256 public maxMintPerTx;

    // Production Events for analytics and transparency
    event MinterAdded(address indexed account);
    event MinterRemoved(address indexed account);
    event MaxMintPerTxUpdated(uint256 oldLimit, uint256 newLimit);

    /**
     * @dev Constructor
     * @param defaultAdmin Address of the Cold Wallet (Owner)
     * @param minter Address of the Backend Hot Wallet (Minter)
     */
    constructor(address defaultAdmin, address minter) ERC20("MEANLY", "MNLY") {
        _grantRole(DEFAULT_ADMIN_ROLE, defaultAdmin);
        _grantRole(MINTER_ROLE, minter);
        
        // Initial safety limit: 10,000 MNLY
        maxMintPerTx = 10000 ether;
        emit MaxMintPerTxUpdated(0, maxMintPerTx);
    }

    /**
     * @dev Mints new tokens. Restricted by MINTER_ROLE and safety limits.
     */
    function mint(address to, uint256 amount) public onlyRole(MINTER_ROLE) whenNotPaused {
        require(to != address(0), "Meanly: mint to the zero address");
        require(amount <= maxMintPerTx, "Meanly: amount exceeds maxMintPerTx");
        _mint(to, amount);
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
     * @dev Gas-efficient batch minting for multiple recipients.
     */
    function batchMint(address[] calldata recipients, uint256[] calldata amounts) 
        external 
        onlyRole(MINTER_ROLE) 
        whenNotPaused 
    {
        require(recipients.length == amounts.length, "Meanly: length mismatch");
        
        for (uint256 i = 0; i < recipients.length; i++) {
            require(recipients[i] != address(0), "Meanly: mint to the zero address");
            require(amounts[i] <= maxMintPerTx, "Meanly: exceeds maxMintPerTx");
            _mint(recipients[i], amounts[i]);
        }
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
     * @dev Hook that is called before any transfer of tokens.
     */
    function _update(address from, address to, uint256 value) internal override whenNotPaused {
        super._update(from, to, value);
    }
}
