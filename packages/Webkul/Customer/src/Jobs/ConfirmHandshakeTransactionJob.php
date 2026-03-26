<?php

namespace Webkul\Customer\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Webkul\Customer\Models\Handshake;
use Webkul\Customer\Services\HotWalletService;
use Webkul\Customer\Services\MatrixService;
use Illuminate\Support\Facades\Log;

class ConfirmHandshakeTransactionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(protected Handshake $handshake)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(HotWalletService $hotWalletService, MatrixService $matrixService): void
    {
        if (!$this->handshake->tx_hash) {
            Log::error("ConfirmHandshakeTransactionJob: Handshake #{$this->handshake->id} has no TX hash.");
            return;
        }

        $receipt = $hotWalletService->getTransactionReceipt($this->handshake->tx_hash);

        if (!$receipt) {
            // Still pending, release back to queue with delay
            $this->release(30); // Try again in 30 seconds
            return;
        }

        if ($receipt['status'] === 'success') {
            Log::info("ConfirmHandshakeTransactionJob: Handshake #{$this->handshake->id} confirmed on-chain.");
            
            $this->handshake->update([
                'status'    => 'accepted',
                'tx_status' => 'confirmed',
            ]);

            // Now that it's confirmed on-chain, create the Matrix room
            $matrixService->createPeerRoom($this->handshake);
            
        } else {
            Log::error("ConfirmHandshakeTransactionJob: Handshake #{$this->handshake->id} transaction failed.");
            
            $this->handshake->update([
                'status'    => 'pending', // Revert to pending so user can try again
                'tx_status' => 'failed',
                'tx_hash'   => null,
            ]);
        }
    }
}
