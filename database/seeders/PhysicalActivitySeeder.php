<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AssessmentQuestion;
use Illuminate\Support\Facades\DB;

class PhysicalActivitySeeder extends Seeder
{
    /**
     * Run the database seeds for physical activity questions.
     *
     * @return void
     */
    public function run()
    {
        // Define the physical activity questions
        $questions = [
            [
                'question_id' => 'regular_exercise',
                'series_id' => 'physical_activity',
                'text' => 'حداقل ۵ روز در هفته ورزش هوازی (مثل پیاده‌روی سریع، دویدن، شنا، دوچرخه‌سواری) حداقل ۳۰ دقیقه انجام می‌دهم',
                'type' => 'checkbox',
                'options' => null,
                'required' => false,
                'order' => 1,
            ],
            [
                'question_id' => 'active_lifestyle',
                'series_id' => 'physical_activity',
                'text' => 'علاوه بر ورزش منظم در طول روز فعالیت زیادی دارم (مثل کارهای بدنی، استفاده کم از ماشین و آسانسور)',
                'type' => 'checkbox',
                'options' => null,
                'required' => false,
                'order' => 2,
            ],
            [
                'question_id' => 'irregular_exercise',
                'series_id' => 'physical_activity',
                'text' => 'در هفته ۲ تا ۴ بار ورزش کرده‌ام، اما نه به‌طور منظم',
                'type' => 'checkbox',
                'options' => null,
                'required' => false,
                'order' => 3,
            ],
            [
                'question_id' => 'light_activity',
                'series_id' => 'physical_activity',
                'text' => 'فعالیت بدنی خاصی ندارم، اما کارهای روزمره مثل خرید، پیاده‌روی یا بالا رفتن از پله‌ها را انجام می‌دهم',
                'type' => 'checkbox',
                'options' => null,
                'required' => false,
                'order' => 4,
            ],
            [
                'question_id' => 'minimal_exercise',
                'series_id' => 'physical_activity',
                'text' => 'کمتر از ۲ بار در هفته فعالیت بدنی متوسط یا ورزش داشته‌ام',
                'type' => 'checkbox',
                'options' => null,
                'required' => false,
                'order' => 5,
            ],
            [
                'question_id' => 'physical_sedentary',
                'series_id' => 'physical_activity',
                'text' => 'بیشتر اوقات روی صندلی یا در حالت نشسته هستم و تقریباً هیچ فعالیت ورزشی ندارم',
                'type' => 'checkbox',
                'options' => null,
                'required' => false,
                'order' => 6,
            ],
            [
                'question_id' => 'physical_limitation',
                'series_id' => 'physical_activity',
                'text' => 'به دلیل مشکلات سلامتی یا محدودیت جسمانی، قادر به انجام ورزش یا فعالیت بدنی نیستم',
                'type' => 'checkbox',
                'options' => null,
                'required' => false,
                'order' => 7,
            ],
        ];

        // Insert physical activity questions
        DB::table('assessment_questions')->insert($questions);

        $this->command->info('Physical activity questions seeded successfully!');
    }
}
