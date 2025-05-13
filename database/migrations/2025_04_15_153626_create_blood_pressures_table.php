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
        Schema::create('blood_pressures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Link to user
            $table->timestamp('date'); // Date of reading
            $table->integer('systolic'); // Systolic pressure
            $table->integer('diastolic'); // Diastolic pressure
            $table->integer('heart_rate'); // Heart rate
            $table->text('notes')->nullable(); // Optional notes
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blood_pressures');
    }
};
