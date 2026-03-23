<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Services\NftService;
use Illuminate\Support\Facades\Log;

class MintNftOnPurchase implements ShouldQueue
{
    use InteractsWithQueue;

    protected $nftService;

    public function __construct(NftService $nftService)
    {
        $this->nftService = $nftService;
    }

    public function handle($invoice)
    {
        Log::info('MintNftOnPurchase listener triggered for Invoice ID: ' . $invoice->id);

        $order = $invoice->order;
        if (!$order) {
            return;
        }

        $customer = $order->customer;
        
        // If guest checkout or customer not found
        if (!$customer) {
            Log::info('No customer attached to order, skipping NFT minting.');
            return;
        }

        $walletAddress = $customer->credits_id;

        if (empty($walletAddress)) {
            Log::info('Customer ' . $customer->id . ' does not have a wallet address (credits_id), skipping NFT minting.');
            return;
        }

        // Use the Order ID as the unique NFT token ID, creating a verified 1:1 receipt!
        $giftId = $order->id;

        Log::info("Attempting to auto-mint NFT Receipt #{$giftId} (Order #{$order->id}) for Customer {$customer->id} ({$walletAddress})");

        $result = $this->nftService->mintGift($walletAddress, $giftId, 1);

        if (!$result['success']) {
            Log::error('Failed to auto-mint NFT on purchase.', ['error' => $result['error']]);
            // Depending on business logic, we could throw an exception to retry the queued job
            // throw new \Exception($result['error']); 
        } else {
            Log::info('Successfully auto-minted NFT on purchase.');
        }
    }
}
