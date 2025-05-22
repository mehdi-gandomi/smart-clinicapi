<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('doctor_blood_pressure_voices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('doctor_id');
            $table->string('blood_pressure_ids');
            $table->string('voice_path');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('doctor_blood_pressure_voices');
    }
}; 