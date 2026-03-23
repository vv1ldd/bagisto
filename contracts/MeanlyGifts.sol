// SPDX-License-Identifier: MIT
pragma solidity ^0.8.20;

/**
 * @title MeanlyGifts
 * @dev Minimal ERC-1155 Multi-Token contract for Meanly gift NFTs.
 *      No external dependencies — can be compiled and deployed with a single `forge create` command.
 *      Only the contract owner (Admin Hot Wallet) can mint new tokens.
 */
contract MeanlyGifts {

    // ==============================================================
    //  State
    // ==============================================================

    address public owner;
    string  public name   = "Meanly Gifts";
    string  public symbol = "MGF";

    // account => id => balance
    mapping(address => mapping(uint256 => uint256)) private _balances;
    // owner => operator => approved
    mapping(address => mapping(address => bool)) private _operatorApprovals;
    // id => custom URI (optional per-token override)
    mapping(uint256 => string) private _tokenURIs;
    // Base URI prefix — tokens default to base + id + ".json"
    string private _baseURI;

    // ==============================================================
    //  Events (ERC-1155 spec)
    // ==============================================================

    event TransferSingle(address indexed operator, address indexed from, address indexed to, uint256 id, uint256 value);
    event TransferBatch(address indexed operator, address indexed from, address indexed to, uint256[] ids, uint256[] values);
    event ApprovalForAll(address indexed account, address indexed operator, bool approved);
    event URI(string value, uint256 indexed id);
    event OwnershipTransferred(address indexed previousOwner, address indexed newOwner);

    // ==============================================================
    //  Constructor
    // ==============================================================

    constructor(address initialOwner) {
        owner    = initialOwner;
        _baseURI = "https://meanly.ru/api/nft/metadata/";
    }

    // ==============================================================
    //  Ownership
    // ==============================================================

    modifier onlyOwner() {
        require(msg.sender == owner, "MeanlyGifts: caller is not the owner");
        _;
    }

    function transferOwnership(address newOwner) external onlyOwner {
        require(newOwner != address(0), "MeanlyGifts: zero address");
        emit OwnershipTransferred(owner, newOwner);
        owner = newOwner;
    }

    // ==============================================================
    //  Admin — Minting
    // ==============================================================

    /**
     * @notice Mint `amount` copies of token `id` to `to`. Only callable by owner.
     * @param to       Recipient address (user's credits_id)
     * @param id       Token type ID (e.g. 1 = "First Purchase Badge")
     * @param amount   Number of tokens (usually 1)
     * @param data     Extra data (pass 0x if none)
     */
    function mint(address to, uint256 id, uint256 amount, bytes calldata data)
        external
        onlyOwner
    {
        require(to != address(0), "MeanlyGifts: mint to zero address");
        _balances[to][id] += amount;
        emit TransferSingle(msg.sender, address(0), to, id, amount);
        _doSafeTransferAcceptanceCheck(msg.sender, address(0), to, id, amount, data);
    }

    /**
     * @notice Batch-mint multiple token types to `to` in one transaction.
     */
    function mintBatch(
        address to,
        uint256[] calldata ids,
        uint256[] calldata amounts,
        bytes calldata data
    ) external onlyOwner {
        require(to != address(0), "MeanlyGifts: mint to zero address");
        require(ids.length == amounts.length, "MeanlyGifts: length mismatch");
        for (uint256 i = 0; i < ids.length; i++) {
            _balances[to][ids[i]] += amounts[i];
        }
        emit TransferBatch(msg.sender, address(0), to, ids, amounts);
    }

    // ==============================================================
    //  Admin — Metadata
    // ==============================================================

    function setBaseURI(string calldata newBaseURI) external onlyOwner {
        _baseURI = newBaseURI;
    }

    function setTokenURI(uint256 id, string calldata tokenURI) external onlyOwner {
        _tokenURIs[id] = tokenURI;
        emit URI(tokenURI, id);
    }

    // ==============================================================
    //  ERC-1155 View Functions
    // ==============================================================

    function uri(uint256 id) external view returns (string memory) {
        if (bytes(_tokenURIs[id]).length > 0) return _tokenURIs[id];
        return string(abi.encodePacked(_baseURI, _uint2str(id), ".json"));
    }

    function balanceOf(address account, uint256 id) external view returns (uint256) {
        return _balances[account][id];
    }

    function balanceOfBatch(
        address[] calldata accounts,
        uint256[] calldata ids
    ) external view returns (uint256[] memory batchBalances) {
        require(accounts.length == ids.length, "MeanlyGifts: length mismatch");
        batchBalances = new uint256[](accounts.length);
        for (uint256 i = 0; i < accounts.length; i++) {
            batchBalances[i] = _balances[accounts[i]][ids[i]];
        }
    }

    function isApprovedForAll(address account, address operator) external view returns (bool) {
        return _operatorApprovals[account][operator];
    }

    function setApprovalForAll(address operator, bool approved) external {
        _operatorApprovals[msg.sender][operator] = approved;
        emit ApprovalForAll(msg.sender, operator, approved);
    }

    function safeTransferFrom(
        address from,
        address to,
        uint256 id,
        uint256 amount,
        bytes calldata data
    ) external {
        require(
            from == msg.sender || _operatorApprovals[from][msg.sender],
            "MeanlyGifts: not approved"
        );
        require(to != address(0), "MeanlyGifts: transfer to zero");
        require(_balances[from][id] >= amount, "MeanlyGifts: insufficient balance");
        _balances[from][id]  -= amount;
        _balances[to][id]    += amount;
        emit TransferSingle(msg.sender, from, to, id, amount);
        _doSafeTransferAcceptanceCheck(msg.sender, from, to, id, amount, data);
    }

    function safeBatchTransferFrom(
        address from,
        address to,
        uint256[] calldata ids,
        uint256[] calldata amounts,
        bytes calldata data
    ) external {
        require(
            from == msg.sender || _operatorApprovals[from][msg.sender],
            "MeanlyGifts: not approved"
        );
        require(to != address(0), "MeanlyGifts: transfer to zero");
        require(ids.length == amounts.length, "MeanlyGifts: length mismatch");
        for (uint256 i = 0; i < ids.length; i++) {
            require(_balances[from][ids[i]] >= amounts[i], "MeanlyGifts: insufficient balance");
            _balances[from][ids[i]] -= amounts[i];
            _balances[to][ids[i]]   += amounts[i];
        }
        emit TransferBatch(msg.sender, from, to, ids, amounts);
    }

    function supportsInterface(bytes4 interfaceId) external pure returns (bool) {
        return
            interfaceId == 0xd9b67a26 || // ERC-1155
            interfaceId == 0x0e89341c || // ERC-1155 MetadataURI
            interfaceId == 0x01ffc9a7;   // ERC-165
    }

    // ==============================================================
    //  Internal Helpers
    // ==============================================================

    function _doSafeTransferAcceptanceCheck(
        address operator,
        address from,
        address to,
        uint256 id,
        uint256 amount,
        bytes memory data
    ) private {
        if (to.code.length > 0) {
            // Call onERC1155Received on the receiving contract
            (bool success, bytes memory result) = to.call(
                abi.encodeWithSignature(
                    "onERC1155Received(address,address,uint256,uint256,bytes)",
                    operator, from, id, amount, data
                )
            );
            require(
                success && bytes4(result) == 0xf23a6e61,
                "MeanlyGifts: transfer rejected by recipient"
            );
        }
    }

    function _uint2str(uint256 value) private pure returns (string memory) {
        if (value == 0) return "0";
        uint256 temp = value;
        uint256 digits;
        while (temp != 0) { digits++; temp /= 10; }
        bytes memory buffer = new bytes(digits);
        while (value != 0) {
            digits -= 1;
            buffer[digits] = bytes1(uint8(48 + uint256(value % 10)));
            value /= 10;
        }
        return string(buffer);
    }
}
