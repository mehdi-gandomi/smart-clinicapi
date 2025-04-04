<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AssessmentQuestion;
use Illuminate\Support\Facades\DB;

class DiagnosticPreferencesSeeder extends Seeder
{
    /**
     * Run the database seeds for diagnostic preferences questions.
     *
     * @return void
     */
    public function run()
    {
        // Define the diagnostic preferences questions
        $questions = [
            [
                'question_id' => 'immediate_tests',
                'series_id' => 'diagnostic_preferences',
                'text' => 'در صورت توصیه پزشک، تمامی تست‌های لازم را در اسرع وقت و در کلینیک انجام می‌دهم',
                'type' => 'checkbox',
                'options' => null,
                'required' => false,
                'order' => 1,
            ],
            [
                'question_id' => 'same_day_tests',
                'series_id' => 'diagnostic_preferences',
                'text' => 'اگر تست‌ها ضروری باشند، آن‌ها را در همان روز انجام می‌دهم تا روند تشخیص سریع‌تر باشد',
                'type' => 'checkbox',
                'options' => null,
                'required' => false,
                'order' => 2,
            ],
            [
                'question_id' => 'consult_first',
                'series_id' => 'diagnostic_preferences',
                'text' => 'ترجیح می‌دهم ابتدا مشاوره بگیرم و بعد تصمیم بگیرم که کدام تست‌ها را انجام دهم',
                'type' => 'checkbox',
                'options' => null,
                'required' => false,
                'order' => 3,
            ],
            [
                'question_id' => 'insurance_coverage',
                'series_id' => 'diagnostic_preferences',
                'text' => 'در صورتی که تست‌های تشخیصی با هزینه مناسب یا تحت پوشش بیمه باشند، آن‌ها را انجام خواهم داد',
                'type' => 'checkbox',
                'options' => null,
                'required' => false,
                'order' => 4,
            ],
            [
                'question_id' => 'financial_limitation',
                'series_id' => 'diagnostic_preferences',
                'text' => 'به‌دلیل محدودیت مالی، ترجیح می‌دهم فقط فقط ویزیت پزشک را انجام دهم و تست‌ها را به تعویق بیندازم',
                'type' => 'checkbox',
                'options' => null,
                'required' => false,
                'order' => 5,
            ],
            [
                'question_id' => 'prefer_public',
                'series_id' => 'diagnostic_preferences',
                'text' => 'ترجیح می‌دهم تست‌های تشخیصی را در مراکز دولتی یا بیمه‌پوشش‌دار انجام دهم',
                'type' => 'checkbox',
                'options' => null,
                'required' => false,
                'order' => 6,
            ],
            [
                'question_id' => 'doctor_recommended',
                'series_id' => 'diagnostic_preferences',
                'text' => 'اگر پزشک تأکید کند که تست‌ها ضروری هستند، برای انجام آن‌ها برنامه‌ریزی می‌کنم',
                'type' => 'checkbox',
                'options' => null,
                'required' => false,
                'order' => 7,
            ],
            [
                'question_id' => 'no_tests_needed',
                'series_id' => 'diagnostic_preferences',
                'text' => 'در حال حاضر نیازی به انجام تست‌های تشخیصی نمی‌بینم، مگر اینکه وضعیت من بدتر شود',
                'type' => 'checkbox',
                'options' => null,
                'required' => false,
                'order' => 8,
            ],
            [
                'question_id' => 'external_tests',
                'series_id' => 'diagnostic_preferences',
                'text' => 'ترجیح می‌دهم تست‌های ضروری را در جای دیگری انجام دهم و فقط گزارش آن‌ها را برای بررسی به پزشک ارائه کنم',
                'type' => 'checkbox',
                'options' => null,
                'required' => false,
                'order' => 9,
            ],
        ];

        // Insert diagnostic preferences questions
        DB::table('assessment_questions')->insert($questions);

        $this->command->info('Diagnostic preferences questions seeded successfully!');
    }
}
