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
        // Add unique constraint to customer_organizations: one customer can only have one organization with a specific INN
        Schema::table('customer_organizations', function (Blueprint $table) {
            $table->unique(['customer_id', 'inn'], 'customer_organizations_customer_inn_unique');
        });

        // Add unique constraint to organization_settlement_accounts: one organization can only have one specific settlement account (BIC + Account)
        Schema::table('organization_settlement_accounts', function (Blueprint $table) {
            $table->unique(['organization_id', 'bic', 'settlement_account'], 'org_settlement_accounts_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customer_organizations', function (Blueprint $table) {
            $table->dropUnique('customer_organizations_customer_inn_unique');
        });

        Schema::table('organization_settlement_accounts', function (Blueprint $table) {
            $table->dropUnique('org_settlement_accounts_unique');
        });
    }
};
