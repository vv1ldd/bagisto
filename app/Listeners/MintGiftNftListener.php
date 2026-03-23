<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

use Illuminate\Support\Facades\Log;
use Webkul\Sales\Models\Order;
use App\Jobs\ProcessNftMintingJob;

class MintGiftNftListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle($order): void
    {
        if (!$order instanceof Order) {
            return;
        }

        // We only mint for registered customers
        if (!$order->customer_id) {
            return;
        }

        Log::info("MintGiftNftListener: Dispatching Minting Job for Order [{$order->id}]");

        // Dispatch background job to mint NFT (Token ID = 1)
        ProcessNftMintingJob::dispatch($order->id, $order->customer_id, 1);
    }
}
