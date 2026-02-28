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
        Schema::table('customer_crypto_addresses', function (Blueprint $table) {
            $table->decimal('verification_amount', 36, 18)->nullable()->after('balance');
            $table->timestamp('verified_at')->nullable()->after('verification_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customer_crypto_addresses', function (Blueprint $table) {
            $table->dropColumn(['verification_amount', 'verified_at']);
        });
    }
};
