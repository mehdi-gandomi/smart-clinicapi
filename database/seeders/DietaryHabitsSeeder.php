<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AssessmentQuestion;
use Illuminate\Support\Facades\DB;

class DietaryHabitsSeeder extends Seeder
{
    /**
     * Run the database seeds for dietary habits questions.
     *
     * @return void
     */
    public function run()
    {
        // Define the dietary habits questions
        $questions = [
            [
                'question_id' => 'healthy_fruits',
                'series_id' => 'dietary_habits',
                'text' => 'روزانه حداقل ۵ واحد میوه و سبزیجات مصرف می‌کنم',
                'type' => 'checkbox',
                'options' => null,
                'required' => false,
                'order' => 1,
            ],
            [
                'question_id' => 'occasional_fruits',
                'series_id' => 'dietary_habits',
                'text' => 'فقط گاهی اوقات میوه و سبزیجات مصرف می‌کنم (کمتر از ۳ بار در هفته)',
                'type' => 'checkbox',
                'options' => null,
                'required' => false,
                'order' => 2,
            ],
            [
                'question_id' => 'rare_fruits',
                'series_id' => 'dietary_habits',
                'text' => 'به‌ندرت میوه و سبزیجات مصرف می‌کنم یا اصلاً در رژیم غذایی‌ام نیست',
                'type' => 'checkbox',
                'options' => null,
                'required' => false,
                'order' => 3,
            ],
            [
                'question_id' => 'low_salt',
                'series_id' => 'dietary_habits',
                'text' => 'مصرف نمک خود را محدود کرده‌ام و از غذاهای کم‌نمک استفاده می‌کنم',
                'type' => 'checkbox',
                'options' => null,
                'required' => false,
                'order' => 4,
            ],
            [
                'question_id' => 'processed_foods',
                'series_id' => 'dietary_habits',
                'text' => 'غذاهای نیمه آماده، کنسروی یا فرآوری‌شده (مثل سوسیس و کالباس) مصرف می‌کنم، اما سعی می‌کنم مصرف نمک را کنترل کنم',
                'type' => 'checkbox',
                'options' => null,
                'required' => false,
                'order' => 5,
            ],
            [
                'question_id' => 'high_salt',
                'series_id' => 'dietary_habits',
                'text' => 'به غذاهای شور علاقه دارم و مصرف نمک زیادی دارم',
                'type' => 'checkbox',
                'options' => null,
                'required' => false,
                'order' => 6,
            ],
            [
                'question_id' => 'healthy_fats',
                'series_id' => 'dietary_habits',
                'text' => 'از روغن‌های گیاهی سالم (مثل روغن زیتون و کانولا) استفاده می‌کنم و چربی‌های اشباع را محدود کرده‌ام',
                'type' => 'checkbox',
                'options' => null,
                'required' => false,
                'order' => 7,
            ],
            [
                'question_id' => 'moderate_fats',
                'series_id' => 'dietary_habits',
                'text' => 'غذاهای سرخ‌کردنی و چرب را کم مصرف می‌کنم، اما همچنان در رژیم غذایی‌ام وجود دارد',
                'type' => 'checkbox',
                'options' => null,
                'required' => false,
                'order' => 8,
            ],
            [
                'question_id' => 'high_fats',
                'series_id' => 'dietary_habits',
                'text' => 'مقدار زیادی غذاهای چرب، سرخ‌کردنی، فست‌فود و کره حیوانی مصرف می‌کنم',
                'type' => 'checkbox',
                'options' => null,
                'required' => false,
                'order' => 9,
            ],
            [
                'question_id' => 'low_sugar',
                'series_id' => 'dietary_habits',
                'text' => 'مصرف قند و شیرینی‌جات را کاهش داده‌ام و بیشتر از غذات کامل استفاده می‌کنم',
                'type' => 'checkbox',
                'options' => null,
                'required' => false,
                'order' => 10,
            ],
            [
                'question_id' => 'moderate_sugar',
                'series_id' => 'dietary_habits',
                'text' => 'گاهی اوقات شیرینی و نوشیدنی‌های شیرین مصرف می‌کنم، اما زیاد نیست',
                'type' => 'checkbox',
                'options' => null,
                'required' => false,
                'order' => 11,
            ],
            [
                'question_id' => 'high_sugar',
                'series_id' => 'dietary_habits',
                'text' => 'مقدار زیادی شیرینی، نوشیدنی‌های قندی و نان سفید مصرف می‌کنم',
                'type' => 'checkbox',
                'options' => null,
                'required' => false,
                'order' => 12,
            ],
            [
                'question_id' => 'healthy_protein',
                'series_id' => 'dietary_habits',
                'text' => 'بیشتر از ماهی، مرغ بدون پوست، حبوبات و پروتئین‌های گیاهی استفاده می‌کنم',
                'type' => 'checkbox',
                'options' => null,
                'required' => false,
                'order' => 13,
            ],
            [
                'question_id' => 'moderate_meat',
                'series_id' => 'dietary_habits',
                'text' => 'گوشت قرمز مصرف می‌کنم، اما مقدار آن را کنترل می‌کنم',
                'type' => 'checkbox',
                'options' => null,
                'required' => false,
                'order' => 14,
            ],
            [
                'question_id' => 'low_fat_dairy',
                'series_id' => 'dietary_habits',
                'text' => 'لبنیات کم‌چرب (مثل شیر و ماست کم‌چرب) مصرف می‌کنم',
                'type' => 'checkbox',
                'options' => null,
                'required' => false,
                'order' => 15,
            ],
            [
                'question_id' => 'high_fat_dairy',
                'series_id' => 'dietary_habits',
                'text' => 'لبنیات پرچرب مصرف می‌کنم',
                'type' => 'checkbox',
                'options' => null,
                'required' => false,
                'order' => 16,
            ],
            [
                'question_id' => 'alcohol_occasional',
                'series_id' => 'dietary_habits',
                'text' => 'گهگاهی الکل یا نوشیدنی‌های مضر مصرف می‌کنم',
                'type' => 'checkbox',
                'options' => null,
                'required' => false,
                'order' => 17,
            ],
            [
                'question_id' => 'alcohol_frequent',
                'series_id' => 'dietary_habits',
                'text' => 'مقدار زیادی الکل، نوشیدنی‌های گازدار یا انرژی‌زا مصرف می‌کنم',
                'type' => 'checkbox',
                'options' => null,
                'required' => false,
                'order' => 18,
            ],
        ];

        // Insert dietary habits questions
        DB::table('assessment_questions')->insert($questions);

        $this->command->info('Dietary habits questions seeded successfully!');
    }
}
