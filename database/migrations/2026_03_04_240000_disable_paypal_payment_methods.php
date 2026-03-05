<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Disable all PayPal payment methods in core_config.
     */
    public function up(): void
    {
        $paypalMethods = [
            'paypal_standard',
            'paypal_smart_button',
        ];

        foreach ($paypalMethods as $method) {
            DB::table('core_config')->updateOrInsert(
                [
                    'code' => "sales.payment_methods.{$method}.active",
                    'channel_code' => null,
                    'locale_code' => null,
                ],
                ['value' => '0']
            );
        }
    }

    /**
     * Re-enable PayPal methods.
     */
    public function down(): void
    {
        DB::table('core_config')
            ->whereIn('code', [
                'sales.payment_methods.paypal_standard.active',
                'sales.payment_methods.paypal_smart_button.active',
            ])
            ->delete();
    }
};
