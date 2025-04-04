<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Assessment Series table (sections)
        Schema::create('assessment_series', function (Blueprint $table) {
            $table->id();
            $table->string('series_id')->unique();
            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('order');
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        // Assessment Questions table
        Schema::create('assessment_questions', function (Blueprint $table) {
            $table->id();
            $table->string('question_id');
            $table->string('series_id');
            $table->text('text');
            $table->string('type')->default('checkbox'); // checkbox, radio, text, etc.
            $table->json('options')->nullable();
            $table->boolean('required')->default(false);
            $table->integer('order');
            $table->boolean('active')->default(true);
            $table->timestamps();

            $table->unique(['question_id', 'series_id']);
        });

        // User Assessments table
        Schema::create('user_assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->boolean('completed')->default(false);
            $table->text('full_prompt')->nullable();
            $table->text('full_response')->nullable();
            $table->text('documents_prompt')->nullable();
            $table->text('documents_response')->nullable();
            $table->text('assessment_prompt')->nullable();
            $table->text('assessment_response')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->string('status')->default('in_progress'); // in_progress, completed, archived
            $table->timestamps();
        });

        // User Assessment Answers table
        Schema::create('user_assessment_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_assessment_id')->constrained()->onDelete('cascade');
            $table->string('question_id');
            $table->string('series_id');
            $table->text('answer')->nullable();
            $table->json('selected_options')->nullable(); // For multiple selections
            $table->timestamps();
        });

        // User Assessment Documents table
        Schema::create('user_assessment_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_assessment_id')->constrained()->onDelete('cascade');
            $table->string('document_type'); // medical_record, drug_image, etc.
            $table->string('file_path');
            $table->string('original_filename');
            $table->string('mime_type');
            $table->integer('file_size');
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_assessment_documents');
        Schema::dropIfExists('user_assessment_answers');
        Schema::dropIfExists('user_assessments');
        Schema::dropIfExists('assessment_questions');
        Schema::dropIfExists('assessment_series');
    }
};
