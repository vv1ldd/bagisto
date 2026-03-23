<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class NftMetadataController extends Controller
{
    /**
     * Return ERC-1155 compliant metadata for a gift ID.
     */
    public function show($id): JsonResponse
    {
        // Mock data. In a real production scenario, this would query
        // a database table (e.g., matching the Product ID to the NFT).
        
        $gifts = [
            1 => [
                'name' => 'Meanly Badge - First Purchase',
                'description' => 'A special badge awarded for the very first transaction on the platform.',
                'image' => 'https://meanly.ru/storage/gifts/first-purchase.png',
                'attributes' => [
                    ['trait_type' => 'Rarity', 'value' => 'Common']
                ]
            ],
            2 => [
                'name' => 'Meanly Gift - Golden Heart',
                'description' => 'A beautifully crafted golden heart to say thank you.',
                'image' => 'https://meanly.ru/storage/gifts/golden-heart.png',
                'attributes' => [
                    ['trait_type' => 'Type', 'value' => 'Premium'],
                    ['trait_type' => 'Rarity', 'value' => 'Epic']
                ]
            ]
        ];

        if (!isset($gifts[$id])) {
            // Default generic fallback if ID is not explicitly outlined
            return response()->json([
                'name' => 'Meanly Gift #' . $id,
                'description' => 'An exclusive digital gift on Meanly.',
                'image' => 'https://meanly.ru/storage/gifts/default.png',
                'attributes' => [
                    ['trait_type' => 'Gift ID', 'value' => (string) $id]
                ]
            ]);
        }

        return response()->json($gifts[$id]);
    }
}
