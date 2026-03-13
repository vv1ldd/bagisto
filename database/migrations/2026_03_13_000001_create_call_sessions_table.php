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
        Schema::create('call_sessions', function (Blueprint $blueprint) {
            $blueprint->id();
            $blueprint->uuid('uuid')->unique()->index();
            $blueprint->string('caller_name')->nullable();
            $blueprint->string('caller_email')->index();
            $blueprint->string('recipient_email')->index();
            $blueprint->string('status')->default('active');
            $blueprint->json('metadata')->nullable();
            $blueprint->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('call_sessions');
    }
};
