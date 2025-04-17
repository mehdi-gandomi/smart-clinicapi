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
        Schema::table('online_visits', function (Blueprint $table) {
            $table->text('answer')->nullable();
            $table->timestamp('answered_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('online_visits', function (Blueprint $table) {
            $table->dropColumn(['answer', 'answered_at']);
        });
    }
};
