<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Webkul\Sales\Models\Order;

class NftMetadataController extends Controller
{
    /**
     * Return ERC-1155 compliant metadata for an Order ID.
     */
    public function show($id): JsonResponse
    {
        $order = Order::find($id);

        if (!$order) {
            return response()->json([
                'name' => 'Meanly Token #' . $id,
                'description' => 'A digital asset on Meanly.',
                'image' => url('/api/nft/image/' . $id . '.svg'),
            ]);
        }

        $tier = $this->determineTier($order->base_grand_total);
        $date = $order->created_at ? $order->created_at->format('M d, Y') : 'Unknown Date';

        return response()->json([
            'name' => "Meanly {$tier['name']} Receipt #{$order->id}",
            'description' => "An exclusive digital receipt commemorating Order #{$order->id} on Meanly.ru.",
            'image' => url("/api/nft/image/{$id}.svg"),
            'attributes' => [
                ['trait_type' => 'Tier', 'value' => $tier['name']],
                ['trait_type' => 'Order Value', 'value' => number_format($order->base_grand_total, 2) . ' ' . $order->order_currency_code],
                ['trait_type' => 'Issue Date', 'value' => $date]
            ]
        ]);
    }

    /**
     * Dynamically generate an SVG receipt/ticket image.
     */
    public function image($id): Response
    {
        $order = Order::find($id);
        
        $amount = $order ? current(explode('.', $order->base_grand_total)) : 0;
        $tier = $this->determineTier($amount);
        
        $title = $order ? "Meanly {$tier['name']} Receipt" : "Meanly Token";
        $orderIdText = $order ? "#" . $order->id : "#" . $id;
        $amountText = $order ? number_format($order->base_grand_total, 2) . ' ' . $order->order_currency_code : '???';

        // Render dynamic SVG card with Meanly logo geometry
        $svg = '<?xml version="1.0" encoding="UTF-8"?>
<svg width="400" height="400" xmlns="http://www.w3.org/2000/svg">
    <defs>
        <linearGradient id="bg" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%" stop-color="' . $tier['bg1'] . '"/>
            <stop offset="100%" stop-color="' . $tier['bg2'] . '"/>
        </linearGradient>
    </defs>
    <!-- Background Card -->
    <rect width="400" height="400" rx="30" fill="url(#bg)" />
    
    <!-- Overlay elements -->
    <circle cx="200" cy="200" r="160" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="2"/>
    <circle cx="200" cy="200" r="120" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="1" stroke-dasharray="10 5"/>

    <!-- Meanly Logo Text (Identical configuration to Meanly SVG) -->
    <text x="200" y="150" font-family="Inter, -apple-system, BlinkMacSystemFont, sans-serif" font-size="52" font-weight="900" fill="#FFFFFF" letter-spacing="-3" text-anchor="middle" style="text-transform: uppercase;">
        MEANLY
    </text>

    <!-- Content -->
    <text x="200" y="240" font-family="sans-serif" font-size="24" font-weight="bold" fill="#FFFFFF" text-anchor="middle">' . $title . '</text>
    <text x="200" y="280" font-family="sans-serif" font-size="20" fill="' . $tier['accent'] . '" text-anchor="middle">Order ' . $orderIdText . '</text>
    <text x="200" y="320" font-family="sans-serif" font-size="28" font-weight="900" fill="#FFFFFF" text-anchor="middle">' . $amountText . '</text>

    <!-- Footer -->
    <text x="200" y="370" font-family="monospace" font-size="12" fill="rgba(255,255,255,0.5)" text-anchor="middle">DIGITAL RECEIPT ON ARBITRUM</text>
</svg>';

        return response($svg)->header('Content-Type', 'image/svg+xml');
    }

    /**
     * Determine token tier based on amount.
     */
    private function determineTier($amount)
    {
        if ($amount >= 50) {
            return [
                'name' => 'Gold',
                'bg1' => '#F59E0B', 
                'bg2' => '#78350F', 
                'accent' => '#FEF3C7' 
            ];
        } elseif ($amount >= 10) {
            return [
                'name' => 'Silver',
                'bg1' => '#9CA3AF', 
                'bg2' => '#374151', 
                'accent' => '#F3F4F6' 
            ];
        } else {
            return [
                'name' => 'Bronze',
                'bg1' => '#D97706', 
                'bg2' => '#78350F', 
                'accent' => '#FEF3C7' 
            ];
        }
    }
}
