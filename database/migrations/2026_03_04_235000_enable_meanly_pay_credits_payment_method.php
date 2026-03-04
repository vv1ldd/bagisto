<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Enable the Meanly Pay (credits) payment method by inserting
     * the core_config record for sales.payment_methods.credits.active.
     */
    public function up(): void
    {
        $configs = [
            [
                'code' => 'sales.payment_methods.credits.active',
                'value' => '1',
                'channel_code' => null,
                'locale_code' => null,
            ],
            [
                'code' => 'sales.payment_methods.credits.title',
                'value' => 'Meanly Pay',
                'channel_code' => null,
                'locale_code' => null,
            ],
            [
                'code' => 'sales.payment_methods.credits.sort',
                'value' => '1',
                'channel_code' => null,
                'locale_code' => null,
            ],
        ];

        foreach ($configs as $config) {
            DB::table('core_config')->updateOrInsert(
                ['code' => $config['code'], 'channel_code' => $config['channel_code'], 'locale_code' => $config['locale_code']],
                ['value' => $config['value']]
            );
        }
    }

    /**
     * Remove the Meanly Pay config entries.
     */
    public function down(): void
    {
        DB::table('core_config')
            ->where('code', 'like', 'sales.payment_methods.credits.%')
            ->delete();
    }
};
