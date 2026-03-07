<?php

namespace Webkul\Core\Helpers\Dadata;

use Illuminate\Support\Facades\Http;

class DadataHelper
{
    /**
     * DaData API Endpoint for party search by ID (INN).
     */
    const FIND_BY_ID_URL = 'https://suggestions.dadata.ru/suggestions/api/4_1/rs/findById/party';

    /**
     * DaData API Endpoint for bank search by BIC.
     */
    const FIND_BANK_BY_ID_URL = 'https://suggestions.dadata.ru/suggestions/api/4_1/rs/findById/bank';

    /**
     * DaData API Endpoint for bank suggestion by query (name, bic, etc.).
     */
    const SUGGEST_BANK_URL = 'https://suggestions.dadata.ru/suggestions/api/4_1/rs/suggest/bank';

    /**
     * Lookup organization by INN.
     *
     * @param  string  $inn
     * @return array|null
     */
    public function lookupOrganization(string $inn): ?array
    {
        $apiKey = config('services.dadata.api_key');

        if (!$apiKey) {
            return null;
        }

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Token ' . $apiKey,
            ])->post(self::FIND_BY_ID_URL, [
                        'query' => $inn,
                    ]);

            if ($response->successful()) {
                $suggestions = $response->json('suggestions');

                if (!empty($suggestions)) {
                    $item = $suggestions[0];

                    return [
                        'name' => $item['value'] ?? null,
                        'inn' => $item['data']['inn'] ?? null,
                        'kpp' => $item['data']['kpp'] ?? null,
                        'address' => $item['data']['address']['value'] ?? null,
                    ];
                }
            }
        } catch (\Exception $e) {
            report($e);
        }

        return null;
    }

    /**
     * Lookup bank by BIC.
     *
     * @param  string  $bic
     * @return array|null
     */
    public function lookupBank(string $bic): ?array
    {
        $apiKey = config('services.dadata.api_key');

        if (!$apiKey) {
            return null;
        }

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Token ' . $apiKey,
            ])->post(self::FIND_BANK_BY_ID_URL, [
                        'query' => $bic,
                    ]);

            if ($response->successful()) {
                $suggestions = $response->json('suggestions');

                if (!empty($suggestions)) {
                    $item = $suggestions[0];

                    return [
                        'bank_name' => $item['value'] ?? null,
                        'correspondent_account' => $item['data']['correspondent_account'] ?? null,
                        'bic' => $item['data']['bic'] ?? null,
                    ];
                }
            }
        } catch (\Exception $e) {
            report($e);
        }

        return null;
    }

    /**
     * Suggest banks by query string (name, SWIFT, BIC).
     *
     * @param  string  $query
     * @return array
     */
    public function suggestBank(string $query): array
    {
        $apiKey = config('services.dadata.api_key');

        if (!$apiKey) {
            return [];
        }

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Token ' . $apiKey,
            ])->post(self::SUGGEST_BANK_URL, [
                        'query' => $query,
                    ]);

            if ($response->successful()) {
                $suggestions = $response->json('suggestions');

                if (!empty($suggestions)) {
                    return array_map(function ($item) {
                        return [
                            'name' => $item['value'] ?? null,
                            'bank_name' => $item['value'] ?? null,
                            'correspondent_account' => $item['data']['correspondent_account'] ?? null,
                            'bic' => $item['data']['bic'] ?? null,
                            'address' => $item['data']['address']['value'] ?? null,
                        ];
                    }, $suggestions);
                }
            }
        } catch (\Exception $e) {
            report($e);
        }

        return [];
    }
}
