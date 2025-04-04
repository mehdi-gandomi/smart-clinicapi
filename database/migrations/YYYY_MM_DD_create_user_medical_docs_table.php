<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_medical_docs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger("user_assessment_id")->nullable();
            $table->enum('doc_type', ['blood_test', 'other']);
            $table->text('description')->nullable();
            $table->json('files');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_medical_docs');
    }
};
