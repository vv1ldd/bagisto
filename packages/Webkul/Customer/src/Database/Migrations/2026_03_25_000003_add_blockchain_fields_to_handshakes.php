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
        Schema::table('handshakes', function (Blueprint $table) {
            $table->string('tx_hash')->nullable()->unique()->after('matrix_room_id');
            $table->string('tx_status')->nullable()->after('tx_hash');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('handshakes', function (Blueprint $table) {
            $table->dropColumn(['tx_hash', 'tx_status']);
        });
    }
};
