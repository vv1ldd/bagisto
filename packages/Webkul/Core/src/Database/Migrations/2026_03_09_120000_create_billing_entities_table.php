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
        Schema::create('billing_entities', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('inn')->nullable();
            $table->string('kpp')->nullable();
            $table->string('address')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('bic')->nullable();
            $table->string('settlement_account')->nullable();
            $table->string('correspondent_account')->nullable();
            $table->string('director_name')->nullable();
            $table->string('accountant_name')->nullable();
            $table->boolean('is_default')->default(0);
            $table->timestamps();
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->integer('billing_entity_id')->unsigned()->nullable();
            $table->foreign('billing_entity_id')->references('id')->on('billing_entities')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropForeign(['billing_entity_id']);
            $table->dropColumn('billing_entity_id');
        });

        Schema::dropIfExists('billing_entities');
    }
};
