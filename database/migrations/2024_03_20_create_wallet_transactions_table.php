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
        Schema::create('wallet_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wallet_id');
            $table->decimal('amount', 12, 0);
            $table->enum('type', ['deposit', 'withdraw', 'transfer']);
            $table->string('description')->nullable();
            $table->string('reference_id')->nullable();
            $table->string('transaction_id')->nullable();
            $table->string('reference_type')->nullable();
            $table->json('metadata')->nullable();
            $table->enum('status', ['pending', 'completed', 'failed'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallet_transactions');
    }
};
