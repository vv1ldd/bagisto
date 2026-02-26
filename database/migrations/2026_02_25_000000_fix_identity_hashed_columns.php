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
        Schema::table('customers', function (Blueprint $table) {
            // gender was string(50), bcrypt needs 60. Using 255 for safety.
            $table->string('gender', 255)->nullable()->change();

            // date_of_birth might still have constraints or be short depending on DB driver.
            $table->string('date_of_birth', 255)->nullable()->comment('Hashed')->change();

            // birth_city is already 255 but let's be explicit and ensure it's string.
            $table->string('birth_city', 255)->nullable()->comment('Hashed')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->string('gender', 50)->nullable()->change();
            // Note: Reverting date_of_birth to date might fail if it contains hashes.
            // Keeping it as string(255) in down for safety if needed, 
            // but the previous migration had date('date_of_birth')->nullable()->change().
        });
    }
};
