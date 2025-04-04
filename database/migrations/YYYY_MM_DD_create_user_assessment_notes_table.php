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
        Schema::create('user_assessment_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_assessment_id')->constrained('user_assessments')->onDelete('cascade');
            $table->string('series_id');
            $table->text('notes');
            $table->timestamps();

            // Add a unique constraint to ensure one note per series per assessment
            $table->unique(['user_assessment_id', 'series_id']);

            // Add foreign key for series_id
            $table->foreign('series_id')
                  ->references('series_id')
                  ->on('assessment_series')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_assessment_notes');
    }
};
