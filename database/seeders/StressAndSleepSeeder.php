<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AssessmentQuestion;
use Illuminate\Support\Facades\DB;

class StressAndSleepSeeder extends Seeder
{
    /**
     * Run the database seeds for stress and sleep questions.
     *
     * @return void
     */
    public function run()
    {
        // Define the stress and sleep questions
        $questions = [
            [
                'question_id' => 'general_anxiety',
                'series_id' => 'stress_and_sleep',
                'text' => 'در طول روز اغلب احساس اضطراب و استرس دارم، حتی بدون دلیل مشخص',
                'type' => 'checkbox',
                'options' => null,
                'required' => false,
                'order' => 1,
            ],
            [
                'question_id' => 'cardiac_stress',
                'series_id' => 'stress_and_sleep',
                'text' => 'معمولاً استرس من با علائم قلبی (مثل تپش قلب، درد قفسه سینه یا تنگی نفس) همراه است',
                'type' => 'checkbox',
                'options' => null,
                'required' => false,
                'order' => 2,
            ],
            [
                'question_id' => 'stress_chest_pain',
                'series_id' => 'stress_and_sleep',
                'text' => 'وقتی استرس دارم، احساس درد یا فشار در قفسه سینه پیدا می‌کنم که با استراحت و آرامش بهتر می‌شود',
                'type' => 'checkbox',
                'options' => null,
                'required' => false,
                'order' => 3,
            ],
            [
                'question_id' => 'unrelated_chest_pain',
                'series_id' => 'stress_and_sleep',
                'text' => 'دردهای قفسه سینه‌ام ارتباطی با استرس ندارند و در زمان‌های مختلف بدون دلیل مشخصی ایجاد می‌شوند',
                'type' => 'checkbox',
                'options' => null,
                'required' => false,
                'order' => 4,
            ],
            [
                'question_id' => 'sleep_anxiety',
                'series_id' => 'stress_and_sleep',
                'text' => 'شب‌ها سخت به خواب می‌روم یا نیمه‌شب از خواب بیدار می‌شوم و احساس نگرانی دارم',
                'type' => 'checkbox',
                'options' => null,
                'required' => false,
                'order' => 5,
            ],
            [
                'question_id' => 'poor_sleep',
                'series_id' => 'stress_and_sleep',
                'text' => 'خوابم کم‌کیفیت است و صبح‌ها احساس خستگی دارم',
                'type' => 'checkbox',
                'options' => null,
                'required' => false,
                'order' => 6,
            ],
            [
                'question_id' => 'stress_no_symptoms',
                'series_id' => 'stress_and_sleep',
                'text' => 'استرس روزمره من زیاد است، اما علائم قلبی من همیشه در شرایط استرس‌زا ظاهر نمی‌شوند',
                'type' => 'checkbox',
                'options' => null,
                'required' => false,
                'order' => 7,
            ],
            [
                'question_id' => 'normal_stress',
                'series_id' => 'stress_and_sleep',
                'text' => 'در مجموع، استرس چندانی ندارم و خوابم تقریباً طبیعی است',
                'type' => 'checkbox',
                'options' => null,
                'required' => false,
                'order' => 8,
            ],
        ];

        // Insert stress and sleep questions
        DB::table('assessment_questions')->insert($questions);

        $this->command->info('Stress and sleep questions seeded successfully!');
    }
}
