<?php

namespace Webkul\Shop\Http\Controllers;

use Illuminate\Routing\Controller;
use Webkul\Sales\Repositories\OrderRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class NFTMetadataController extends Controller
{
    public function __construct(protected OrderRepository $orderRepository)
    {
    }

    /**
     * Returns the ERC-721 metadata JSON for a given order.
     */
    public function metadata(int $orderId)
    {
        $order = $this->orderRepository->find($orderId);

        if (!$order) {
            return response()->json(['error' => 'Order not found'], 404);
        }

        $price = core()->formatBasePrice($order->base_grand_total);
        $date = $order->created_at->format('Y-m-d');

        return response()->json([
            'name' => "Meanly Gift #{$order->increment_id}",
            'description' => "Digital proof of purchase and loyalty gift from Meanly. Thank you for your support!",
            'image' => route('shop.nft.image', ['id' => $order->id]),
            'attributes' => [
                [
                    'trait_type' => 'Order Number',
                    'value' => "#{$order->increment_id}"
                ],
                [
                    'trait_type' => 'Purchase Value',
                    'value' => $price
                ],
                [
                    'trait_type' => 'Purchase Date',
                    'value' => $date
                ],
                [
                    'trait_type' => 'Network',
                    'value' => 'Arbitrum One'
                ]
            ]
        ]);
    }

    /**
     * Returns a dynamic SVG image for the NFT.
     */
    public function image(int $orderId)
    {
        $order = $this->orderRepository->find($orderId);

        if (!$order) {
            return response('Order not found', 404);
        }

        $svg = $this->generateSvg($order);

        return response($svg)->header('Content-Type', 'image/svg+xml');
    }

    protected function generateSvg($order)
    {
        $id = "#" . $order->increment_id;
        $date = $order->created_at->format('M d, Y');
        $amount = core()->formatBasePrice($order->base_grand_total);
        $totalItems = $order->total_item_count;

        return <<<SVG
<svg width="1000" height="1000" viewBox="0 0 1000 1000" xmlns="http://www.w3.org/2000/svg">
    <defs>
        <linearGradient id="mainGrad" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%" style="stop-color:#7C45F5;stop-opacity:1" />
            <stop offset="100%" style="stop-color:#1a0b36;stop-opacity:1" />
        </linearGradient>
        
        <linearGradient id="neonGrad" x1="0%" y1="0%" x2="100%" y2="0%">
            <stop offset="0%" style="stop-color:#D6FF00;stop-opacity:1" />
            <stop offset="100%" style="stop-color:#ffffff;stop-opacity:1" />
        </linearGradient>

        <pattern id="grid" width="40" height="40" patternUnits="userSpaceOnUse">
            <path d="M 40 0 L 0 0 0 40" fill="none" stroke="#ffffff" stroke-width="0.5" opacity="0.1"/>
        </pattern>

        <filter id="glow">
            <feGaussianBlur stdDeviation="8" result="blur" />
            <feComposite in="SourceGraphic" in2="blur" operator="over" />
        </filter>
        
        <clipPath id="cardClip">
            <rect x="50" y="50" width="900" height="900" rx="40" ry="40" />
        </clipPath>
    </defs>

    <!-- Background -->
    <rect width="1000" height="1000" fill="#000000" />
    <rect width="1000" height="1000" fill="url(#mainGrad)" opacity="0.4" />
    <rect width="1000" height="1000" fill="url(#grid)" />

    <!-- Card Body (Glassmorphism) -->
    <g clip-path="url(#cardClip)">
        <rect x="50" y="50" width="900" height="900" rx="40" ry="40" fill="#ffffff" opacity="0.03" />
        <rect x="50" y="50" width="900" height="900" rx="40" ry="40" stroke="#ffffff" stroke-width="1.5" opacity="0.2" fill="none" />
        
        <!-- Top Noise/Glint -->
        <path d="M 50 200 Q 500 150 950 200" fill="none" stroke="#ffffff" stroke-width="1" opacity="0.1" />
    </g>

    <!-- Branding -->
    <text x="120" y="150" fill="#ffffff" font-family="'Orbitron', 'Arial Black', sans-serif" font-size="28" font-weight="900" letter-spacing="10" opacity="0.6">MEANLY ASSET</text>
    
    <!-- Central Visual Element -->
    <circle cx="500" cy="480" r="220" fill="none" stroke="#D6FF00" stroke-width="2" opacity="0.3" filter="url(#glow)" />
    <circle cx="500" cy="480" r="180" fill="none" stroke="#ffffff" stroke-width="0.5" opacity="0.2" />
    
    <g transform="translate(500, 480)">
       <text x="0" y="20" fill="url(#neonGrad)" font-family="Arial, sans-serif" font-size="160" font-weight="900" text-anchor="middle" filter="url(#glow)">M</text>
    </g>

    <!-- Bottom Data Block -->
    <rect x="50" y="750" width="900" height="200" fill="#000000" opacity="0.6" />
    <rect x="50" y="750" width="900" height="4" fill="#D6FF00" filter="url(#glow)" />

    <!-- Order Info Tags -->
    <g transform="translate(100, 810)">
        <text x="0" y="0" fill="#D6FF00" font-family="monospace" font-size="14" font-weight="bold" letter-spacing="2">RECEIPT_UNIQUE_ID</text>
        <text x="0" y="45" fill="#ffffff" font-family="Arial, sans-serif" font-size="42" font-weight="900">$id</text>
    </g>

    <g transform="translate(420, 810)">
        <text x="0" y="0" fill="#D6FF00" font-family="monospace" font-size="14" font-weight="bold" letter-spacing="2">VALUE_TOTAL</text>
        <text x="0" y="45" fill="#ffffff" font-family="Arial, sans-serif" font-size="36" font-weight="900">$amount</text>
    </g>

    <g transform="translate(720, 810)">
        <text x="0" y="0" fill="#D6FF00" font-family="monospace" font-size="14" font-weight="bold" letter-spacing="2">TIMESTAMP</text>
        <text x="0" y="45" fill="#ffffff" font-family="Arial, sans-serif" font-size="28" font-weight="bold">$date</text>
    </g>

    <!-- Side Decoration (Vertical Text) -->
    <text transform="translate(935, 700) rotate(-90)" fill="#ffffff" font-family="monospace" font-size="12" font-weight="bold" opacity="0.3" letter-spacing="4">BLOCKCHAIN VERIFIED • DIGITAL RECEIPT • ARBITRUM ONE NETWORK</text>
</svg>
SVG;
    }

    protected function getLogoPath()
    {
        $logoUrl = core()->getCurrentChannel()?->logo_url;

        if ($logoUrl) {
            return "<image href=\"{$logoUrl}\" x=\"200\" y=\"200\" width=\"400\" height=\"300\" />";
        }

        // Fallback: glowing circle if no logo set
        return '<circle cx="380" cy="360" r="180" fill="url(#logoGradient)" filter="url(#glow)" />';
    }
}
