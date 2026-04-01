<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Webkul\Customer\Repositories\CustomerRepository;
use Webkul\Customer\Models\CustomerTransaction;

class ProcessWelcomeMintingJob implements ShouldQueue
{
    use Queueable;

    public $customerId;
    public $amount;

    /**
     * Create a new job instance.
     * 
     * @param int $customerId
     * @param float $amount The welcome bonus amount in coins
     */
    public function __construct(int $customerId, float $amount = 10.0)
    {
        $this->customerId = $customerId;
        $this->amount = $amount;
    }

    /**
     * Execute the job.
     */
    public function handle(
        \Webkul\Customer\Services\HotWalletService $hotWalletService,
        CustomerRepository $customerRepository,
        \Webkul\Customer\Services\BlockchainSyncService $syncService
    ) {
        $customer = $customerRepository->find($this->customerId);

        if (!$customer) {
            Log::error("ProcessWelcomeMintingJob: Customer [{$this->customerId}] not found.");
            return;
        }

        // 1. Double check if already received welcome bonus
        $exists = CustomerTransaction::where('customer_id', $customer->id)
            ->whereIn('type', ['registration_minting', 'welcome_bonus'])
            ->exists();
            
        if ($exists) {
            Log::warning("ProcessWelcomeMintingJob: Customer [{$customer->id}] already received welcome bonus.");
            return;
        }

        // 2. Ensure customer has a blockchain address (credits_id)
        if (empty($customer->credits_id) || !str_starts_with($customer->credits_id, '0x')) {
            Log::error("ProcessWelcomeMintingJob: Customer [{$customer->id}] has no valid Arbitrum address in 'credits_id'.");
            return;
        }

        Log::info("ProcessWelcomeMintingJob: Starting Welcome Minting for Customer [{$customer->id}] (Address: {$customer->credits_id}).");

        // 3. Mint Meanly Coin (ERC20)
        $reason = "Registration Bonus";
        
        try {
            $txHash = $hotWalletService->mintCoin($customer, $this->amount, $reason);
        } catch (\Exception $e) {
            Log::error("ProcessWelcomeMintingJob: Exception during minting for Customer [{$customer->id}]: " . $e->getMessage());
            throw $e;
        }

        if ($txHash) {
            CustomerTransaction::create([
                'uuid' => \Illuminate\Support\Str::uuid(),
                'customer_id' => $customer->id,
                'amount' => $this->amount,
                'type' => 'registration_minting',
                'status' => 'pending', // Will be confirmed 'completed' by BlockchainSyncService when balance appears
                'notes' => "Минтинг Meanly Coins (Регистрация +{$this->amount})",
                'metadata' => [
                    'tx_hash' => $txHash,
                    'network' => 'arbitrum_one',
                    'reason'  => $reason,
                    'triggered_by' => 'ProcessWelcomeMintingJob'
                ],
            ]);
            
            // 5. Update internal balance immediately (optimistic update)
            $customer->increment('balance', $this->amount);
            
            // 6. Sync crypto address record
            try {
                $address = $customer->crypto_addresses()->where('network', 'arbitrum_one')->first();
                if ($address) {
                    $syncService->syncBalance($address);
                }
            } catch (\Exception $e) {
                Log::warning("ProcessWelcomeMintingJob: Sync balance failed for Customer [{$customer->id}], but minting was sent: " . $e->getMessage());
            }

            Log::info("ProcessWelcomeMintingJob: Transaction sent successfully. Tx: {$txHash}");
        } else {
            Log::error("ProcessWelcomeMintingJob: Failed minting for Customer [{$customer->id}] - check HotWalletService logs.");
            throw new \Exception("Welcome Minting submission failed.");
        }
    }
}
