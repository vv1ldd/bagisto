// SPDX-License-Identifier: MIT
pragma solidity ^0.8.20;

import "@openzeppelin/contracts/token/ERC20/ERC20.sol";
import "@openzeppelin/contracts/access/AccessControl.sol";
import "@openzeppelin/contracts/utils/Pausable.sol";

/**
 * @title MeanlyCoin
 * @dev ERC20 Token for Cashback system with production safety features.
 */
contract MeanlyCoin is ERC20, AccessControl, Pausable {
    bytes32 public constant MINTER_ROLE = keccak256("MINTER_ROLE");
    
    // Safety limit to prevent accidental or malicious mass emission
    uint256 public maxMintPerTx;

    /**
     * @dev Constructor
     * @param defaultAdmin Address of the Cold Wallet (Owner)
     * @param minter Address of the Backend Hot Wallet (Minter)
     */
    constructor(address defaultAdmin, address minter) ERC20("MEANLY", "MNLY") {
        _grantRole(DEFAULT_ADMIN_ROLE, defaultAdmin);
        _grantRole(MINTER_ROLE, minter);
        
        // Initial safety limit: 10,000 MNLY (can be changed by Admin)
        maxMintPerTx = 10000 * 10**decimals();
    }

    /**
     * @dev Mints new tokens. Restricted by MINTER_ROLE and safety limits.
     */
    function mint(address to, uint256 amount) public onlyRole(MINTER_ROLE) whenNotPaused {
        require(amount <= maxMintPerTx, "Meanly: amount exceeds maxMintPerTx");
        _mint(to, amount);
    }

    /**
     * @dev Updates the safety limit. Only Admin (Cold Wallet) can call this.
     */
    function setMaxMintPerTx(uint256 newLimit) public onlyRole(DEFAULT_ADMIN_ROLE) {
        maxMintPerTx = newLimit;
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
