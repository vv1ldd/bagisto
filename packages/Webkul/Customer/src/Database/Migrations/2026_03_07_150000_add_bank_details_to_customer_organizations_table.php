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
        Schema::table('customer_organizations', function (Blueprint $table) {
            $table->string('bank_name')->nullable()->after('address');
            $table->string('bic')->nullable()->after('bank_name');
            $table->string('settlement_account')->nullable()->after('bic');
            $table->string('correspondent_account')->nullable()->after('settlement_account');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customer_organizations', function (Blueprint $table) {
            $table->dropColumn(['bank_name', 'bic', 'settlement_account', 'correspondent_account']);
        });
    }
};
