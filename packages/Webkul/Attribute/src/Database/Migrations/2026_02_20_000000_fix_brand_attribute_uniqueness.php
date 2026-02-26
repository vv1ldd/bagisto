<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('attributes')
            ->where('code', 'brand')
            ->update([
                'is_unique' => 0,
                'is_required' => 0,
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('attributes')
            ->where('code', 'brand')
            ->update([
                'is_unique' => 1,
            ]);
    }
};
