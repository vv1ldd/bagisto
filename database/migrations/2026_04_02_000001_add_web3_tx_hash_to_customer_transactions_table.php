<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('customer_transactions', function (Blueprint $table) {
            if (!Schema::hasColumn('customer_transactions', 'web3_tx_hash')) {
                $table->string('web3_tx_hash')->nullable()->after('status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customer_transactions', function (Blueprint $table) {
            $table->dropColumn('web3_tx_hash');
        });
    }
};
