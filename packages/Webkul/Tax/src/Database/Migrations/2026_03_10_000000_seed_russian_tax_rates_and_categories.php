<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $now = Carbon::now();

        // 1. Ensure Tax Rates for Russia
        $rates = [
            ['identifier' => 'ru-vat-20', 'tax_rate' => 20.0000],
            ['identifier' => 'ru-vat-5', 'tax_rate' => 5.0000],
            ['identifier' => 'ru-vat-7', 'tax_rate' => 7.0000],
        ];

        $rateIds = [];
        foreach ($rates as $rateData) {
            $taxRateId = DB::table('tax_rates')->where('identifier', $rateData['identifier'])->value('id');

            if (!$taxRateId) {
                $taxRateId = DB::table('tax_rates')->insertGetId([
                    'identifier' => $rateData['identifier'],
                    'is_zip' => 0,
                    'state' => '*',
                    'country' => 'RU',
                    'tax_rate' => $rateData['tax_rate'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
            $rateIds[$rateData['identifier']] = $taxRateId;
        }

        // 2. Ensure Tax Categories
        $categories = [
            [
                'code' => 'standard-vat-20',
                'name' => 'Standard VAT (20%)',
                'description' => 'Standard Value Added Tax for Russia (20%)',
                'rate_id' => $rateIds['ru-vat-20'],
            ],
            [
                'code' => 'usn-vat-5',
                'name' => 'USN VAT (5%)',
                'description' => 'Special VAT for USN regime (5%)',
                'rate_id' => $rateIds['ru-vat-5'],
            ],
            [
                'code' => 'usn-vat-7',
                'name' => 'USN VAT (7%)',
                'description' => 'Special VAT for USN regime (7%)',
                'rate_id' => $rateIds['ru-vat-7'],
            ],
        ];

        foreach ($categories as $catData) {
            $taxCategoryId = DB::table('tax_categories')->where('code', $catData['code'])->value('id');

            if (!$taxCategoryId) {
                $taxCategoryId = DB::table('tax_categories')->insertGetId([
                    'code' => $catData['code'],
                    'name' => $catData['name'],
                    'description' => $catData['description'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }

            // 3. Link Rate to Category
            $mappingExists = DB::table('tax_mappings')
                ->where('tax_category_id', $taxCategoryId)
                ->where('tax_rate_id', $catData['rate_id'])
                ->exists();

            if (!$mappingExists) {
                DB::table('tax_mappings')->insert([
                    'tax_category_id' => $taxCategoryId,
                    'tax_rate_id' => $catData['rate_id'],
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Typically migrations like this are not reversed to avoid deleting user custom data, 
        // but we can remove the mapping and the rate/category if they match our IDs.

        $taxRateIdentifiers = ['ru-vat-20', 'ru-vat-5', 'ru-vat-7'];
        $taxCategoryCodes = ['standard-vat-20', 'usn-vat-5', 'usn-vat-7'];

        foreach ($taxCategoryCodes as $code) {
            $taxCategoryId = DB::table('tax_categories')->where('code', $code)->value('id');
            if ($taxCategoryId) {
                DB::table('tax_mappings')->where('tax_category_id', $taxCategoryId)->delete();
                DB::table('tax_categories')->where('id', $taxCategoryId)->delete();
            }
        }

        foreach ($taxRateIdentifiers as $identifier) {
            DB::table('tax_rates')->where('identifier', $identifier)->delete();
        }
    }
};
