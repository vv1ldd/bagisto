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
        Schema::create('customer_trusted_devices', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('customer_id');
            $table->string('ip_address', 45); // IPv6 support
            $table->text('user_agent')->nullable();
            $table->string('cookie_token', 64)->unique();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamps();

            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_trusted_devices');
    }
};
