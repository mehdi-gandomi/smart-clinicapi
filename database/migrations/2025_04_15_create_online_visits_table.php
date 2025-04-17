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
        Schema::create('online_visits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('visit_type', ['medical_questions', 'document_review', 'prescription_renewal']);
            $table->text('description')->nullable();
            $table->json('voice_note_path')->nullable();
            $table->json('medical_documents')->nullable(); // Store array of document paths
            $table->enum('status', ['pending', 'in_progress', 'answered', 'cancelled'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('online_visits');
    }
};
