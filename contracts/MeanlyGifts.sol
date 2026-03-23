// SPDX-License-Identifier: MIT
pragma solidity ^0.8.20;

import "@openzeppelin/contracts/token/ERC1155/ERC1155.sol";
import "@openzeppelin/contracts/access/Ownable.sol";
import "@openzeppelin/contracts/utils/Strings.sol";

contract MeanlyGifts is ERC1155, Ownable {
    string public name = "Meanly Gifts";
    string public symbol = "MGF";

    // Base URI for metadata (e.g. Pinata/IPFS or your website API)
    string private _baseTokenURI;

    // Optional mapping to store custom URIs per token ID if they differ from the base URI pattern
    mapping(uint256 => string) private _tokenURIs;

    constructor(address initialOwner) ERC1155("") Ownable(initialOwner) {
        _baseTokenURI = "https://meanly.ru/api/nft/metadata/";
    }

    /**
     * @dev Sets a new base URI for all tokens. Can only be called by the owner.
     */
    function setBaseURI(string memory newuri) public onlyOwner {
        _baseTokenURI = newuri;
    }

    /**
     * @dev Mint a new gift NFT or multiple copies. Can only be called by the central platform (Owner).
     * @param account The recipient's wallet address.
     * @param id The Token ID (e.g. 1 = "First Purchase", 2 = "VIP Ticket").
     * @param amount The number of tokens to mint (usually 1).
     * @param data Any extra arbitrary data (can be empty "0x").
     */
    function mint(address account, uint256 id, uint256 amount, bytes memory data)
        public
        onlyOwner
    {
        _mint(account, id, amount, data);
    }

    /**
     * @dev Mint multiple types of gifts to an account in one transaction.
     */
    function mintBatch(address to, uint256[] memory ids, uint256[] memory amounts, bytes memory data)
        public
        onlyOwner
    {
        _mintBatch(to, ids, amounts, data);
    }

    /**
     * @dev Optionally set a specific URI for a specific Token ID.
     */
    function setURI(uint256 tokenId, string memory tokenURI) public onlyOwner {
        _tokenURIs[tokenId] = tokenURI;
        emit URI(uri(tokenId), tokenId);
    }

    /**
     * @dev Overrides the ERC1155 uri function to return either the custom URI or append the tokenId to the base URI.
     */
    function uri(uint256 tokenId) public view override returns (string memory) {
        string memory customURI = _tokenURIs[tokenId];
        
        if (bytes(customURI).length > 0) {
            return customURI;
        }

        return string(abi.encodePacked(_baseTokenURI, Strings.toString(tokenId), ".json"));
    }
}
