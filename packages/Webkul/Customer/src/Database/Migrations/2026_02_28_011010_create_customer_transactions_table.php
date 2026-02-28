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
        Schema::create('customer_transactions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('uuid')->unique();
            $table->integer('customer_id')->unsigned();
            $table->decimal('amount', 12, 4);
            $table->string('type'); // deposit, withdrawal, purchase, refund, adjustment
            $table->string('status')->default('completed'); // pending, completed, failed, reversed
            $table->string('reference_type')->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->text('notes')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->index(['reference_type', 'reference_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_transactions');
    }
};
