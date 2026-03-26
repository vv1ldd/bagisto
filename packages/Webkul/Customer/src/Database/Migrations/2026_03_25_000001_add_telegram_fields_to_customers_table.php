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
        Schema::table('customers', function (Blueprint $column) {
            $column->bigInteger('telegram_chat_id')->nullable()->unique()->after('credits_alias');
            $column->string('telegram_token', 64)->nullable()->unique()->after('telegram_chat_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $column) {
            $column->dropColumn(['telegram_chat_id', 'telegram_token']);
        });
    }
};
