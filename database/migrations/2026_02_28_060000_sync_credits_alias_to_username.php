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
        // Sync credits_alias to username where username is empty or differs but credits_alias is set
        DB::table('customers')
            ->whereNotNull('credits_alias')
            ->where(function ($query) {
                $query->whereNull('username')
                    ->orWhereRaw('username != credits_alias');
            })
            ->update([
                'username' => DB::raw('credits_alias')
            ]);

        // For users who had neither, generate a default username if booted logic wasn't triggered before
        // But booted logic should handle it on next save. Still, let's ensure uniqueness for any NULLs.
        DB::table('customers')
            ->whereNull('username')
            ->get()
            ->each(function ($customer) {
                $alias = 'u_' . strtolower(bin2hex(random_bytes(5)));
                DB::table('customers')->where('id', $customer->id)->update(['username' => $alias]);
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No effective rollback needed for data sync
    }
};
