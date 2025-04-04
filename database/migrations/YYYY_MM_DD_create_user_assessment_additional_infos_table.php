<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_assessment_additional_infos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_assessment_id')->nullable();
            $table->text('text_description')->nullable();
            $table->string('voice_path')->nullable();
            $table->string('voice_duration')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_assessment_additional_infos');
    }
};
