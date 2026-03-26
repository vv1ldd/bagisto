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
            $column->string('matrix_user_id')->nullable()->unique()->after('telegram_token');
            $column->text('matrix_access_token')->nullable()->after('matrix_user_id');
        });

        Schema::table('handshakes', function (Blueprint $table) {
            $table->string('matrix_room_id')->nullable()->unique()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $column) {
            $column->dropColumn(['matrix_user_id', 'matrix_access_token']);
        });

        Schema::table('handshakes', function (Blueprint $table) {
            $table->dropColumn(['matrix_room_id']);
        });
    }
};
