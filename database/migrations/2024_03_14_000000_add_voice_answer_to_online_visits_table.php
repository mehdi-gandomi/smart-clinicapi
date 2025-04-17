<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('online_visits', function (Blueprint $table) {
            $table->text('voice_answer')->nullable()->after('answer');
            $table->string('voice_answer_duration')->nullable()->after('voice_answer');
        });
    }

    public function down()
    {
        Schema::table('online_visits', function (Blueprint $table) {
            $table->dropColumn('voice_answer');
            $table->dropColumn('voice_answer_duration');
        });
    }
};
