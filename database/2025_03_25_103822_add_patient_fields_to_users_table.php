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
        Schema::table('users', function (Blueprint $table) {
            $table->string('national_id')->nullable()->after('email');
            $table->string('gender')->nullable()->after('national_id');
            $table->integer('age')->nullable()->after('gender');
            $table->integer('weight')->nullable()->after('age'); // وزن بیمار
            $table->integer('height')->nullable()->after('weight'); // قد بیمار
            $table->string('primary_insurance')->nullable()->after('height'); // بیمه پایه
            $table->string('supplementary_insurance')->nullable()->after('primary_insurance'); // بیمه تکمیلی
            $table->string('occupation')->nullable()->after('supplementary_insurance'); // شغل بیمار
            $table->text('address')->nullable()->after('occupation'); // آدرس
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'national_id',
                'gender',
                'age',
                'weight',
                'height',
                'primary_insurance',
                'supplementary_insurance',
                'occupation',
                'address'
            ]);
        });
    }
};
