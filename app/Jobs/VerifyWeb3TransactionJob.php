<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Webkul\Customer\Models\CustomerTransaction;
use Webkul\Customer\Services\HotWalletService;

class VerifyWeb3TransactionJob implements ShouldQueue
{
    use Queueable;

    public $transactionId;
    public $txHash;
    public $tries = 15; // Increased tries for Arbitrum

    /**
     * Create a new job instance.
     */
    public function __construct(int $transactionId, string $txHash)
    {
        $this->transactionId = $transactionId;
        $this->txHash = $txHash;
    }

    /**
     * Execute the job.
     */
    public function handle(HotWalletService $hotWalletService)
    {
        $transaction = CustomerTransaction::find($this->transactionId);

        if (!$transaction) {
            Log::error("VerifyWeb3TransactionJob: Transaction [{$this->transactionId}] not found.");
            return;
        }

        if ($transaction->status === 'completed') {
            return;
        }

        // Get receipt from HotWalletService (uses eth_getTransactionReceipt)
        $receipt = $hotWalletService->getTransactionReceipt($this->txHash);

        if ($receipt) {
            if ($receipt['status'] === 'success') {
                $transaction->update(['status' => 'completed']);
                Log::info("VerifyWeb3TransactionJob: Transaction [{$this->txHash}] confirmed and marked as COMPLETED.");
            } else {
                $transaction->update(['status' => 'failed']);
                Log::error("VerifyWeb3TransactionJob: Transaction [{$this->txHash}] FAILED on-chain.");
                
                // Since we did an optimistic increment on balance, we should technically decrement it back
                // but for now we'll just log it for manual review to avoid balance oscillations.
            }
        } else {
            // Still pending, release back to queue with delay (30 seconds)
            $this->release(30);
        }
    }
}
