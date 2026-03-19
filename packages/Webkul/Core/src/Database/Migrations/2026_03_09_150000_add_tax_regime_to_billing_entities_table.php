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
        Schema::table('billing_entities', function (Blueprint $table) {
            $table->string('tax_regime')->nullable()->after('accountant_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('billing_entities', function (Blueprint $table) {
            $table->dropColumn('tax_regime');
        });
    }
};
