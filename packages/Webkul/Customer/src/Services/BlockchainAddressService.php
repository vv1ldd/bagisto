<?php

namespace Webkul\Customer\Services;

use phpseclib3\Crypt\Hash;
use phpseclib3\Crypt\EC;
use phpseclib3\Math\BigInteger;
use Illuminate\Support\Facades\Log;

class BlockchainAddressService
{
    /**
     * Derive public key and its hash from mnemonic.
     *
     * @param string|array $mnemonic
     * @param string $passphrase
     * @return array|null [public_key, public_key_hash]
     */
    public function derivePublicKeyData($mnemonic, string $passphrase = ''): ?array
    {
        try {
            if (is_array($mnemonic)) {
                $mnemonic = implode(' ', $mnemonic);
            }

            $salt = "mnemonic" . $passphrase;
            $seed = hash_pbkdf2('sha512', $mnemonic, $salt, 2048, 64, true);

            $hmac = new Hash('sha512');
            $hmac->setKey('Bitcoin seed');
            $I = $hmac->hash($seed);
            
            $masterPrivateKey = substr($I, 0, 32);
            $masterChainCode = substr($I, 32, 32);

            $path = [44 | 0x80000000, 60 | 0x80000000, 0 | 0x80000000, 0, 0];
            $currKey = $masterPrivateKey;
            $currChainCode = $masterChainCode;

            foreach ($path as $index) {
                list($currKey, $currChainCode) = $this->deriveChildKey($currKey, $currChainCode, $index);
            }

            $curve = new \phpseclib3\Crypt\EC\Curves\secp256k1();
            $privateKeyInt = new BigInteger(bin2hex($currKey), 16);
            $publicKeyPoint = $curve->multiplyPoint($curve->getBasePoint(), $privateKeyInt);
            $affinePoint = $curve->convertToAffine($publicKeyPoint);
            
            $xHex = str_pad($affinePoint[0]->toBigInteger()->toHex(), 64, '0', STR_PAD_LEFT);
            $yHex = str_pad($affinePoint[1]->toBigInteger()->toHex(), 64, '0', STR_PAD_LEFT);
            $publicKeyBin = hex2bin('04' . $xHex . $yHex); // Uncompressed SEC format

            $publicKeyHex = bin2hex($publicKeyBin);
            
            // Hash the public key (SHA-256 for general purposes)
            $sha256 = new Hash('sha256');
            $publicKeyHash = bin2hex($sha256->hash($publicKeyBin));

            return [
                'public_key'      => $publicKeyHex,
                'public_key_hash' => $publicKeyHash,
            ];

        } catch (\Exception $e) {
            Log::error("Public key derivation failed: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Derive a standard Ethereum/Arbitrum One address from a BIP39 mnemonic.
     * Uses the standard derivation path: m/44'/60'/0'/0/0
     *
     * @param string|array $mnemonic
     * @param string $passphrase Optional BIP39 passphrase
     * @return string|null 0x-prefixed address
     */
    public function deriveEthereumAddress($mnemonic, string $passphrase = ''): ?string
    {
        try {
            if (is_array($mnemonic)) {
                $mnemonic = implode(' ', $mnemonic);
            }

            // 1. Generate Seed (BIP39)
            $salt = "mnemonic" . $passphrase;
            $seed = hash_pbkdf2('sha512', $mnemonic, $salt, 2048, 64, true);

            // 2. Derive Master Private Key (BIP32)
            $hmac = new Hash('sha512');
            $hmac->setKey('Bitcoin seed');
            $I = $hmac->hash($seed);
            
            $masterPrivateKey = substr($I, 0, 32);
            $masterChainCode = substr($I, 32, 32);

            // 3. Follow Derivation Path: m/44'/60'/0'/0/0
            $path = [
                44 | 0x80000000,
                60 | 0x80000000,
                0  | 0x80000000,
                0,
                0
            ];

            $currKey = $masterPrivateKey;
            $currChainCode = $masterChainCode;

            foreach ($path as $index) {
                list($currKey, $currChainCode) = $this->deriveChildKey($currKey, $currChainCode, $index);
            }

            // 4. Derive Public Key from Private Key (SECP256K1) using raw curve math
            $curve = new \phpseclib3\Crypt\EC\Curves\secp256k1();
            $privateKeyInt = new BigInteger(bin2hex($currKey), 16);
            
            // Public Key = Private Key * G
            $publicKeyPoint = $curve->multiplyPoint($curve->getBasePoint(), $privateKeyInt);
            $affinePoint = $curve->convertToAffine($publicKeyPoint);
            
            // Ethereum address uses raw X and Y coordinates (32 bytes each)
            $xHex = str_pad($affinePoint[0]->toBigInteger()->toHex(), 64, '0', STR_PAD_LEFT);
            $yHex = str_pad($affinePoint[1]->toBigInteger()->toHex(), 64, '0', STR_PAD_LEFT);
            $publicKeyBin = hex2bin($xHex . $yHex);

            // 5. Generate Ethereum Address (Keccak256 hash of public key)
            $keccak = new Hash('keccak256');
            $hash = $keccak->hash($publicKeyBin);
            
            // Link address is the last 20 bytes of the hash
            $address = '0x' . substr(bin2hex($hash), -40);

            return $this->toChecksumAddress($address);

        } catch (\Exception $e) {
            Log::error("Ethereum address derivation failed: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Derive a child key from parent key and chain code.
     */
    protected function deriveChildKey(string $parentKey, string $parentChainCode, int $index): array
    {
        $hmac = new Hash('sha512');
        $hmac->setKey($parentChainCode);

        $curve = new \phpseclib3\Crypt\EC\Curves\secp256k1();

        if ($index >= 0x80000000) {
            // Hardened derivation: HMAC-SHA512(ChainCode, 0x00 || ParentPrivateKey || Index)
            $data = "\x00" . $parentKey . pack('N', $index);
        } else {
            // Normal derivation: HMAC-SHA512(ChainCode, ParentPublicKey || Index)
            $parentKeyInt = new BigInteger(bin2hex($parentKey), 16);
            $parentPublicKeyPoint = $curve->multiplyPoint($curve->getBasePoint(), $parentKeyInt);
            $affinePoint = $curve->convertToAffine($parentPublicKeyPoint);
            
            // Compressed public key: 0x02 or 0x03 prefix + X coordinate
            $prefix = $affinePoint[1]->toBigInteger()->isOdd() ? "\x03" : "\x02";
            $parentPublicKey = $prefix . hex2bin(str_pad($affinePoint[0]->toBigInteger()->toHex(), 64, '0', STR_PAD_LEFT));
            
            $data = $parentPublicKey . pack('N', $index);
        }

        $I = $hmac->hash($data);
        
        $IL = substr($I, 0, 32);
        $IR = substr($I, 32, 32);

        // Child Private Key = (IL + ParentPrivateKey) % n
        $n = new BigInteger('FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFEBAAEDCE6AF48A03BBFD25E8CD0364141', 16);
        $IL_int = new BigInteger(bin2hex($IL), 16);
        $parentKey_int = new BigInteger(bin2hex($parentKey), 16);
        
        // Use divide()[1] for modulo in phpseclib3 BigInteger if mod() is missing
        $sum = $IL_int->add($parentKey_int);
        list(, $childKey_int) = $sum->divide($n);
        
        $childKey = hex2bin(str_pad($childKey_int->toHex(), 64, '0', STR_PAD_LEFT));

        return [$childKey, $IR];
    }

    /**
     * Convert address to EIP-55 Checksum Address.
     */
    public function toChecksumAddress(string $address): string
    {
        $address = strtolower(str_replace('0x', '', $address));
        $keccak = new Hash('keccak256');
        $hash = bin2hex($keccak->hash($address));
        
        $checksumAddress = '0x';
        for ($i = 0; $i < 40; $i++) {
            if (hexdec($hash[$i]) >= 8) {
                $checksumAddress .= strtoupper($address[$i]);
            } else {
                $checksumAddress .= $address[$i];
            }
        }
        
        return $checksumAddress;
    }
}
