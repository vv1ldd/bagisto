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

        // Placeholder for the brand logo provided by the user
        $logoPath = $this->getLogoPath();

        return <<<SVG
<svg width="1000" height="1000" viewBox="0 0 1000 1000" xmlns="http://www.w3.org/2000/svg">
    <defs>
        <linearGradient id="bgGradient" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%" style="stop-color:#1a0b36;stop-opacity:1" />
            <stop offset="100%" style="stop-color:#000000;stop-opacity:1" />
        </linearGradient>
        <linearGradient id="logoGradient" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%" style="stop-color:#ffffff;stop-opacity:1" />
            <stop offset="100%" style="stop-color:#8e44ad;stop-opacity:1" />
        </linearGradient>
        <filter id="glow">
            <feGaussianBlur stdDeviation="15" result="blur" />
            <feComposite in="SourceGraphic" in2="blur" operator="over" />
        </filter>
    </defs>
    
    <!-- Background -->
    <rect width="1000" height="1000" fill="url(#bgGradient)" />
    
    <!-- Decorative elements -->
    <circle cx="200" cy="200" r="150" fill="#ffffff" opacity="0.05" />
    <circle cx="850" cy="750" r="100" fill="#8e44ad" opacity="0.1" />
    
    <!-- Logo Container -->
    <g transform="translate(120, 150) scale(1)">
        $logoPath
    </g>
    
    <!-- Labels -->
    <text x="50" y="880" fill="#ffffff" font-family="Arial, sans-serif" font-size="24" opacity="0.6" font-weight="bold">MEANLY GIFT</text>
    <text x="50" y="930" fill="#ffffff" font-family="Arial, sans-serif" font-size="48" font-weight="bold">$id</text>
    
    <text x="950" y="880" fill="#ffffff" font-family="Arial, sans-serif" font-size="24" text-anchor="end" opacity="0.6">DATE</text>
    <text x="950" y="930" fill="#ffffff" font-family="Arial, sans-serif" font-size="32" text-anchor="end" font-weight="bold">$date</text>
    
    <text x="500" y="930" fill="#ffffff" font-family="Arial, sans-serif" font-size="32" text-anchor="middle" font-weight="bold" opacity="0.8">$amount</text>
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
