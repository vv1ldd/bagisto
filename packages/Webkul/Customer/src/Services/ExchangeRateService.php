<?php

namespace Webkul\Customer\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ExchangeRateService
{
    /**
     * Get the live exchange rate for a crypto symbol in the base fiat currency.
     * Caches the result for 10 minutes to avoid hitting API rate limits.
     *
     * @param string $cryptoSymbol 'bitcoin', 'ethereum', etc.
     * @param string $fiatCurrency 'USD', 'RUB', etc.
     * @return float
     */
    public function getRate(string $cryptoSymbol, string $fiatCurrency = null): float
    {
        $cryptoSymbol = strtolower($cryptoSymbol);
        $fiatCurrency = strtoupper($fiatCurrency ?? core()->getBaseCurrencyCode());

        // Map internal symbols to CoinGecko API IDs
        $coinGeckoIdMap = [
            'ton' => 'the-open-network',
            'usdt_ton' => 'tether',
        ];

        $apiId = $coinGeckoIdMap[$cryptoSymbol] ?? $cryptoSymbol;

        $cacheKey = "exchange_rate_{$cryptoSymbol}_{$fiatCurrency}";

        return Cache::remember($cacheKey, 600, function () use ($apiId, $fiatCurrency) {
            try {
                $response = Http::get("https://api.coingecko.com/api/v3/simple/price", [
                    'ids' => $apiId,
                    'vs_currencies' => strtolower($fiatCurrency)
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    return (float) ($data[$apiId][strtolower($fiatCurrency)] ?? 0);
                }

                Log::warning("Failed to fetch exchange rate for {$apiId}/{$fiatCurrency} from CoinGecko. Status: " . $response->status());
            } catch (\Exception $e) {
                Log::error("Error fetching exchange rate for {$apiId}/{$fiatCurrency}: " . $e->getMessage());
            }

            // Fallback or return 0 if failed
            return 0.0;
        });
    }
}
