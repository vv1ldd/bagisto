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
        if (!Schema::hasTable('customer_login_logs')) {
            Schema::create('customer_login_logs', function (Blueprint $table) {
                $table->id();
                $table->integer('customer_id')->unsigned();
                $table->string('session_id')->nullable()->index();
                $table->string('ip_address')->nullable();
                $table->text('user_agent')->nullable();
                $table->string('device_name')->nullable();
                $table->string('platform')->nullable();
                $table->string('browser')->nullable();
                $table->timestamp('last_active_at')->nullable();
                $table->timestamp('logged_out_at')->nullable();
                $table->timestamps();

                $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_login_logs');
    }
};
