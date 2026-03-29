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
            ->where('type', 'welcome_bonus')
            ->exists();
            
        if ($exists) {
            Log::warning("ProcessWelcomeMintingJob: Customer [{$customer->id}] already received welcome bonus.");
            return;
        }

        Log::info("ProcessWelcomeMintingJob: Starting Welcome Minting for Customer [{$customer->id}].");

        // 2. Mint Registration Meanly Coin (ERC20)
        $reason = "Registration Bonus";
        $txHash = $hotWalletService->mintCoin($customer, $this->amount, $reason);

        if ($txHash) {
            // 3. Record transaction
            CustomerTransaction::create([
                'uuid' => \Illuminate\Support\Str::uuid(),
                'customer_id' => $customer->id,
                'amount' => $this->amount,
                'type' => 'registration_minting',
                'status' => 'completed',
                'notes' => 'Минтинг Meanly Coins (Регистрация)',
                'metadata' => [
                    'tx_hash' => $txHash,
                    'network' => 'arbitrum_one',
                    'reason'  => $reason
                ],
            ]);
            
            // 4. Update internal balance immediately
            $customer->increment('balance', $this->amount);
            
            // 5. Sync crypto address record
            $address = $customer->crypto_addresses()->where('network', 'arbitrum_one')->first();
            if ($address) {
                // We don't wait for on-chain confirmation here, but we sync what we can
                $syncService->syncBalance($address);
            }

            Log::info("ProcessWelcomeMintingJob: Completed. Tx: {$txHash}");
        } else {
            Log::error("ProcessWelcomeMintingJob: Failed minting for Customer [{$customer->id}].");
            throw new \Exception("Welcome Minting Failed.");
        }
    }
}
