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
        Schema::create('handshakes', function (Blueprint $blueprint) {
            $blueprint->id();
            
            $blueprint->integer('sender_id')->unsigned();
            $blueprint->integer('receiver_id')->unsigned();
            
            $blueprint->string('status')->default('pending'); // pending, accepted, declined, disconnected
            
            $blueprint->foreign('sender_id')->references('id')->on('customers')->onDelete('cascade');
            $blueprint->foreign('receiver_id')->references('id')->on('customers')->onDelete('cascade');

            $blueprint->unique(['sender_id', 'receiver_id']);
            
            $blueprint->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('handshakes');
    }
};
