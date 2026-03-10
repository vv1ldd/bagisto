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

        // 1. Ensure Tax Rate for Russia 20%
        $taxRateIdentifier = 'ru-vat-20';
        $taxRateId = DB::table('tax_rates')->where('identifier', $taxRateIdentifier)->value('id');

        if (!$taxRateId) {
            $taxRateId = DB::table('tax_rates')->insertGetId([
                'identifier' => $taxRateIdentifier,
                'is_zip' => 0,
                'state' => '*',
                'country' => 'RU',
                'tax_rate' => 20.0000,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        // 2. Ensure Tax Category "Standard VAT (20%)"
        $taxCategoryCode = 'standard-vat-20';
        $taxCategoryId = DB::table('tax_categories')->where('code', $taxCategoryCode)->value('id');

        if (!$taxCategoryId) {
            $taxCategoryId = DB::table('tax_categories')->insertGetId([
                'code' => $taxCategoryCode,
                'name' => 'Standard VAT (20%)',
                'description' => 'Standard Value Added Tax for Russia (20%)',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        // 3. Link Rate to Category
        $mappingExists = DB::table('tax_mappings')
            ->where('tax_category_id', $taxCategoryId)
            ->where('tax_rate_id', $taxRateId)
            ->exists();

        if (!$mappingExists) {
            DB::table('tax_mappings')->insert([
                'tax_category_id' => $taxCategoryId,
                'tax_rate_id' => $taxRateId,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Typically migrations like this are not reversed to avoid deleting user custom data, 
        // but we can remove the mapping and the rate/category if they match our IDs.

        $taxRateIdentifier = 'ru-vat-20';
        $taxCategoryCode = 'standard-vat-20';

        $taxRateId = DB::table('tax_rates')->where('identifier', $taxRateIdentifier)->value('id');
        $taxCategoryId = DB::table('tax_categories')->where('code', $taxCategoryCode)->value('id');

        if ($taxRateId && $taxCategoryId) {
            DB::table('tax_mappings')
                ->where('tax_category_id', $taxCategoryId)
                ->where('tax_rate_id', $taxRateId)
                ->delete();

            DB::table('tax_categories')->where('id', $taxCategoryId)->delete();
            DB::table('tax_rates')->where('id', $taxRateId)->delete();
        }
    }
};
