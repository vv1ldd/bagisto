<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('organization_settlement_accounts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('organization_id');
            $table->string('bic');
            $table->string('bank_name');
            $table->string('correspondent_account')->nullable();
            $table->string('settlement_account');
            $table->boolean('is_default')->default(false);
            $table->timestamps();

            // Depending on how customer_organizations ID column is structured, it might be BigInteger or Integer. 
            // In bagisto, standard models use integer increments. Let's adjust based on the parent table structure.
        });

        // Let's first quickly verify the type of customer_organizations.id to ensure the foreign key matches... Wait, I will adjust this after looking at the parent migration.
        // Actually, in `2026_03_03_184000_create_customer_organizations_table.php`, $table->id() is used which defaults to UnsignedBigInteger in Laravel 8+. Let's assume unsignedBigInteger.

        Schema::table('organization_settlement_accounts', function (Blueprint $table) {
            $table->foreign('organization_id')->references('id')->on('customer_organizations')->onDelete('cascade');
        });

        // Migrate existing data
        $organizations = DB::table('customer_organizations')->whereNotNull('settlement_account')->get();

        foreach ($organizations as $org) {
            DB::table('organization_settlement_accounts')->insert([
                'organization_id' => $org->id,
                'bic' => $org->bic,
                'bank_name' => $org->bank_name,
                'correspondent_account' => $org->correspondent_account,
                'settlement_account' => $org->settlement_account,
                'is_default' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organization_settlement_accounts');
    }
};
