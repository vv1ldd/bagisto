<?php

namespace Webkul\Customer\Services;

use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Crypt;
use Spatie\LaravelPasskeys\Actions\FindPasskeyToAuthenticateAction;
use Webkul\Customer\Models\Customer;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class PasskeyWeb3Signer
{
    protected FindPasskeyToAuthenticateAction $findPasskeyAction;
    protected HotWalletService $hotWalletService;

    public function __construct(
        FindPasskeyToAuthenticateAction $findPasskeyAction,
        HotWalletService $hotWalletService
    ) {
        $this->findPasskeyAction = $findPasskeyAction;
        $this->hotWalletService = $hotWalletService;
    }

    /**
     * Re-validates the Passkey biometric signature from the frontend, 
     * decrypts the cold crypto key, and relays the Gasless transaction via Node.js.
     */
    public function processGaslessCheckout(Customer $customer, array $passkeyAssertion, float $amount): string
    {
        // 1. Validate the Passkey Assertion
        $optionsJson = session()->get('passkey-authentication-options-json');
        
        if (empty($optionsJson)) {
            throw new Exception("Срок действия сессии истек. Обновите страницу корзины.");
        }

        try {
            $passkey = $this->findPasskeyAction->execute(
                json_encode($passkeyAssertion),
                $optionsJson
            );

            if (!$passkey || $passkey->authenticatable->id !== $customer->id) {
                throw new Exception("Неверная биометрическая подпись (Passkey) устройства.");
            }

            // Clean up session
            session()->forget('passkey-authentication-options-json');

        } catch (Exception $e) {
            Log::error("Web3 Checkout Passkey Validation Failed", ['customer' => $customer->id, 'error' => $e->getMessage()]);
            throw new Exception("Биометрическое подтверждение не прошло: " . substr($e->getMessage(), 0, 100));
        }

        // 2. Retrieve & Decrypt the Customer's Crypto Private Key
        if (empty($customer->encrypted_private_key)) {
            throw new Exception("У вашего аккаунта нет активированного W1 Wallet.");
        }

        try {
            // Decrypt the isolated private key exclusively for this transaction context
            $privateKey = Crypt::decryptString($customer->encrypted_private_key);
        } catch (Exception $e) {
            Log::error("Crypto Key Decryption Error during Checkout", ['customer' => $customer->id]);
            throw new Exception("Ошибка доступа к вашему W1 Wallet. Обратитесь в поддержку.");
        }

        // 3. Delegate to NodeJS Ethers Gasless Relayer
        return $this->relayGaslessTransaction($privateKey, $amount);
    }

    private function relayGaslessTransaction(string $customerPrivateKey, float $amount): string
    {
        // Gather Environment variables for Relayer node script
        $hotWalletPrivateKey = config('crypto.hot_wallet_private_key');
        $tokenAddress = config('crypto.meanly_coin_address');
        $rpcUrl = config('crypto.rpc_url_arbitrum', 'https://arb1.arbitrum.io/rpc');
        
        // Target address for checkout payments is strictly the merchant's cold wallet
        $merchantTargetAddress = config('crypto.merchant_cold_wallet', config('crypto.hot_wallet_address')); 

        if (empty($hotWalletPrivateKey) || empty($tokenAddress)) {
            throw new Exception("Окружение смарт-контракта не настроено.");
        }

        // Pre-flight check: Does the hot wallet have enough GAS for Arbitrum?
        $hotWalletAddress = config('crypto.hot_wallet_address');
        if ($hotWalletAddress) {
            $balance = $this->hotWalletService->getBalance($hotWalletAddress);
            if ($balance < 0.0001) { // Arbitrum L2 gas is usually < 0.0001 ETH
                Log::error("Web3 Checkout: Hot Wallet [{$hotWalletAddress}] has insufficient gas balance ({$balance} ETH).");
                throw new Exception("Ошибка сервиса оплаты: Недостаточно средств для оплаты комиссии сети. Обратитесь в поддержку.");
            }
        }

        $payload = [
            'customerPrivateKey' => $customerPrivateKey,
            'targetAddress' => $merchantTargetAddress,
            'amountStr' => (string) $amount,
            'tokenAddress' => $tokenAddress,
            'hotWalletPrivateKey' => $hotWalletPrivateKey,
            'rpcUrl' => $rpcUrl
        ];

        // Execute the Ethers JS Script securely via standard input (no params injection)
        $scriptPath = base_path('blockchain/scripts/gasless_relayer.js');
        $process = new Process(['node', $scriptPath]);
        
        // Pass payload securely via STDIN, not arguments
        $process->setInput(json_encode($payload));
        
        try {
            $process->mustRun();
            $outputStr = $process->getOutput();
            $output = json_decode($outputStr, true);
            
            if (!empty($output['success']) && $output['success'] === true) {
                return $output['tx_hash'];
            }
            
            throw new Exception($output['error'] ?? "Неизвестная ошибка блокчейна: " . $outputStr);
            
        } catch (ProcessFailedException $exception) {
            $stdout = $exception->getProcess()->getOutput();
            $stderr = $exception->getProcess()->getErrorOutput();

            Log::error("Node.js Gasless Execution Failed", [
                'stdout' => $stdout,
                'stderr' => $stderr
            ]);

            // Attempt to extract error message from JSON in stdout if possible
            $errorMsg = "Блокчейн отклонил транзакцию. ";
            $jsonOutput = json_decode($stdout, true);
            if (!empty($jsonOutput['error'])) {
                $errorMsg .= $jsonOutput['error'];
            } else {
                $errorMsg .= "Проверьте баланс комиссии или лимиты. (Details: " . substr($stderr ?: $stdout, 0, 100) . ")";
            }

            throw new Exception($errorMsg);
        }
    }
}
