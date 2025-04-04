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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('name')->nullable();
            $table->string('avatar')->nullable();
            $table->string('email')->unique()->nullable();
            $table->string('mobile')->unique()->nullable();
            $table->string('google_id')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->tinyInteger("done_assessment")->default(0);
            $table->timestamp("done_assessment_at")->nullable();
            $table->string("user_type",20)->default("user");//user,admin,doctor

               // Add new fields from the image
               $table->string('national_id')->nullable();
               $table->string('gender')->nullable();
               $table->integer('age')->nullable();
               $table->integer('weight')->nullable(); // وزن بیمار
               $table->integer('height')->nullable(); // قد بیمار
               $table->string('primary_insurance')->nullable(); // بیمه پایه
               $table->string('supplementary_insurance')->nullable(); // بیمه تکمیلی
               $table->string('occupation')->nullable(); // شغل بیمار
               $table->text('address')->nullable(); // آدرس

            $table->string('password')->nullable();
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
};
