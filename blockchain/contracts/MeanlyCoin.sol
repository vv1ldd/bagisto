// SPDX-License-Identifier: MIT
pragma solidity ^0.8.20;

import "@openzeppelin/contracts/token/ERC20/ERC20.sol";
import "@openzeppelin/contracts/access/AccessControl.sol";

/**
 * @title MeanlyCoin
 * @dev ERC20 Token for Cashback system. 
 * Allows a designated minter (hot wallet) to mint new tokens to users' addresses.
 * The Admin (cold wallet) manages the minter roles.
 */
contract MeanlyCoin is ERC20, AccessControl {
    bytes32 public constant MINTER_ROLE = keccak256("MINTER_ROLE");

    /**
     * @dev Constructor
     * @param defaultAdmin Address of the Cold Wallet (Owner)
     * @param minter Address of the Backend Hot Wallet (Minter)
     */
    constructor(address defaultAdmin, address minter) ERC20("Meanly", "MNL") {
        _grantRole(DEFAULT_ADMIN_ROLE, defaultAdmin);
        _grantRole(MINTER_ROLE, minter);
    }

    /**
     * @dev Mints new tokens to a buyer's address as cashback.
     * Can only be called by an account with the MINTER_ROLE.
     */
    function mint(address to, uint256 amount) public onlyRole(MINTER_ROLE) {
        _mint(to, amount);
    }
}
