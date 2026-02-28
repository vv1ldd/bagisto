<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('customer_crypto_transactions', function (Blueprint $table) {
            $table->decimal('exchange_rate', 18, 4)->nullable()->after('amount');
            $table->decimal('fiat_amount', 18, 4)->nullable()->after('exchange_rate');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customer_crypto_transactions', function (Blueprint $table) {
            $table->dropColumn(['exchange_rate', 'fiat_amount']);
        });
    }
};
