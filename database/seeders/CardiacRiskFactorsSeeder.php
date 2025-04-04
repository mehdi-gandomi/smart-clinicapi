<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AssessmentQuestion;
use Illuminate\Support\Facades\DB;

class CardiacRiskFactorsSeeder extends Seeder
{
    /**
     * Run the database seeds for cardiac risk factor questions.
     *
     * @return void
     */
    public function run()
    {
        // Define the cardiac risk factors questions
        $questions = [
            [
                'question_id' => 'family_history',
                'series_id' => 'cardiac_risk_factors',
                'text' => 'سابقه خانوادگی بیماری قلبی و عروقی (والدین، خواهر یا برادر مبتلا به بیماری قلبی یا سکته قلبی داشته‌اند)',
                'type' => 'checkbox',
                'options' => null,
                'required' => false,
                'order' => 1,
            ],
            [
                'question_id' => 'high_blood_pressure',
                'series_id' => 'cardiac_risk_factors',
                'text' => 'فشار خون بالا (قبلاً تشخیص داده شده یا داروی فشار خون مصرف می‌کنم)',
                'type' => 'checkbox',
                'options' => null,
                'required' => false,
                'order' => 2,
            ],
            [
                'question_id' => 'diabetes',
                'series_id' => 'cardiac_risk_factors',
                'text' => 'دیابت یا قند خون بالا (قبلاً تشخیص داده شده یا داروی دیابت مصرف می‌کنم)',
                'type' => 'checkbox',
                'options' => null,
                'required' => false,
                'order' => 3,
            ],
            [
                'question_id' => 'high_cholesterol',
                'series_id' => 'cardiac_risk_factors',
                'text' => 'چربی خون بالا (LDL بالا یا چربی خون بالا در آزمایشات اخیر داشته‌ام یا دارو چربی مصرف می کنم)',
                'type' => 'checkbox',
                'options' => null,
                'required' => false,
                'order' => 4,
            ],
            [
                'question_id' => 'overweight',
                'series_id' => 'cardiac_risk_factors',
                'text' => 'اضافه‌وزن یا چاقی (BMI بالاتر از حد نرمال دارم)',
                'type' => 'checkbox',
                'options' => null,
                'required' => false,
                'order' => 5,
            ],
            [
                'question_id' => 'smoking',
                'series_id' => 'cardiac_risk_factors',
                'text' => 'سیگار یا قلیان (به‌طور منظم یا گاهی اوقات مصرف می‌کنم)',
                'type' => 'checkbox',
                'options' => null,
                'required' => false,
                'order' => 6,
            ],
            [
                'question_id' => 'alcohol',
                'series_id' => 'cardiac_risk_factors',
                'text' => 'مصرف الکل (بیش از حد متعارف الکل مصرف می‌کنم)',
                'type' => 'checkbox',
                'options' => null,
                'required' => false,
                'order' => 7,
            ],
            [
                'question_id' => 'sedentary',
                'series_id' => 'cardiac_risk_factors',
                'text' => 'کم‌تحرکی و عدم فعالیت بدنی (ورزش منظم ندارم و سبک زندگی کم‌تحرکی دارم)',
                'type' => 'checkbox',
                'options' => null,
                'required' => false,
                'order' => 8,
            ],
            [
                'question_id' => 'poor_diet',
                'series_id' => 'cardiac_risk_factors',
                'text' => 'رژیم غذایی ناسالم (مصرف زیاد نمک، چربی‌های اشباع، فست‌فود و کمبود میوه و سبزیجات)',
                'type' => 'checkbox',
                'options' => null,
                'required' => false,
                'order' => 9,
            ],
            [
                'question_id' => 'stress',
                'series_id' => 'cardiac_risk_factors',
                'text' => 'استرس زیاد و تنش عصبی (در معرض تنش‌های روانی مداوم یا فشار کاری بالا هستم)',
                'type' => 'checkbox',
                'options' => null,
                'required' => false,
                'order' => 10,
            ],
            [
                'question_id' => 'sleep_disorders',
                'series_id' => 'cardiac_risk_factors',
                'text' => 'اختلالات خواب (کم‌خوابی یا مشکلات خواب مانند وقفه تنفسی در خواب دارم)',
                'type' => 'checkbox',
                'options' => null,
                'required' => false,
                'order' => 11,
            ],
            [
                'question_id' => 'kidney_problems',
                'series_id' => 'cardiac_risk_factors',
                'text' => 'مشکلات کلیوی (نارسایی کلیه یا کراتینین بالاتر از حد نرمال دارم)',
                'type' => 'checkbox',
                'options' => null,
                'required' => false,
                'order' => 12,
            ],
            [
                'question_id' => 'thyroid',
                'series_id' => 'cardiac_risk_factors',
                'text' => 'بیماری‌های تیروئیدی (کم‌کاری یا پرکاری تیروئید داشته‌ام)',
                'type' => 'checkbox',
                'options' => null,
                'required' => false,
                'order' => 13,
            ],
            [
                'question_id' => 'stroke',
                'series_id' => 'cardiac_risk_factors',
                'text' => 'سابقه سکته مغزی یا حمله ایسکمیک گذرا (TIA) (حمله کوتاه‌مدت مغزی بدون آسیب دائمی داشته‌ام)',
                'type' => 'checkbox',
                'options' => null,
                'required' => false,
                'order' => 14,
            ],
            [
                'question_id' => 'arrhythmia',
                'series_id' => 'cardiac_risk_factors',
                'text' => 'آریتمی‌های قلبی (تپش قلب نامنظم یا مشکلات ریتم قلب داشته‌ام)',
                'type' => 'checkbox',
                'options' => null,
                'required' => false,
                'order' => 15,
            ],
            [
                'question_id' => 'heart_failure',
                'series_id' => 'cardiac_risk_factors',
                'text' => 'نارسایی قلبی (تشخیص قلبی نارسایی قلب یا علائم مرتبط مانند تنگی نفس و ورم دارم)',
                'type' => 'checkbox',
                'options' => null,
                'required' => false,
                'order' => 16,
            ],
            [
                'question_id' => 'none',
                'series_id' => 'cardiac_risk_factors',
                'text' => 'هیچکدام از موارد بالا',
                'type' => 'checkbox',
                'options' => null,
                'required' => false,
                'order' => 17,
            ],
        ];

        // Insert cardiac risk factors questions
        DB::table('assessment_questions')->insert($questions);

        $this->command->info('Cardiac risk factors questions seeded successfully!');
    }
}
