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
        Schema::table('admins', function (Blueprint $table) {
            $table->string('credits_id')->nullable()->after('status');
            $table->string('credits_alias')->nullable()->after('credits_id');
            $table->text('mnemonic_hash')->nullable()->after('credits_alias');
            $table->text('encrypted_private_key')->nullable()->after('mnemonic_hash');
            $table->timestamp('mnemonic_verified_at')->nullable()->after('encrypted_private_key');
            $table->text('public_key')->nullable()->after('mnemonic_verified_at');
            $table->string('public_key_hash')->nullable()->after('public_key');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('admins', function (Blueprint $table) {
            $table->dropColumn([
                'credits_id',
                'credits_alias',
                'mnemonic_hash',
                'encrypted_private_key',
                'mnemonic_verified_at',
                'public_key',
                'public_key_hash',
            ]);
        });
    }
};
