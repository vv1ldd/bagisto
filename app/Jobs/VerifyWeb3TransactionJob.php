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
    public function handle(\Webkul\Customer\Services\BlockchainSyncService $syncService)
    {
        $transaction = CustomerTransaction::find($this->transactionId);

        if (!$transaction) {
            Log::error("VerifyWeb3TransactionJob: Transaction [{$this->transactionId}] not found.");
            return;
        }

        // If already processed (especially by on-demand UI sync), just finish
        if ($transaction->status === 'completed' || $transaction->status === 'failed') {
            return;
        }

        // Use centralized logic to verify and update
        $processed = $syncService->verifyAndStatusUpdate($transaction);

        if (!$processed) {
            // Still pending on-chain, release back to queue with delay (30 seconds)
            $this->release(30);
        }
    }
}
