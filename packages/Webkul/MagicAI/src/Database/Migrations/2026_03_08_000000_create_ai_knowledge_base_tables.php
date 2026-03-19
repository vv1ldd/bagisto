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
        Schema::create('ai_knowledge_items', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->longText('content');
            $table->string('source')->default('manual');
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        Schema::create('ai_embeddings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ai_knowledge_item_id')->constrained('ai_knowledge_items')->onDelete('cascade');
            $table->longText('embedding'); // Store as JSON array of floats
            $table->string('model');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_embeddings');
        Schema::dropIfExists('ai_knowledge_items');
    }
};
