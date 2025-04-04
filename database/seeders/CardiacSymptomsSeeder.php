<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AssessmentQuestion;
use Illuminate\Support\Facades\DB;

class CardiacSymptomsSeeder extends Seeder
{
    /**
     * Run the database seeds for cardiac symptoms questions.
     *
     * @return void
     */
    public function run()
    {
        // Define the cardiac symptoms questions
        $questions = [
            [
                'question_id' => 'chest_pain_activity',
                'series_id' => 'cardiac_symptoms',
                'text' => 'درد یا فشار در قفسه سینه هنگام فعالیت بدنی',
                'type' => 'checkbox',
                'options' => null,
                'required' => false,
                'order' => 1,
            ],
            [
                'question_id' => 'chest_pain_rest',
                'series_id' => 'cardiac_symptoms',
                'text' => 'درد یا سوزش در قفسه سینه که در حالت استراحت هم ایجاد می‌شود',
                'type' => 'checkbox',
                'options' => null,
                'required' => false,
                'order' => 2,
            ],
            [
                'question_id' => 'chest_pain_radiating',
                'series_id' => 'cardiac_symptoms',
                'text' => 'درد قفسه سینه که به دست چپ، گردن، فک یا پشت انتشار پیدا می‌کند',
                'type' => 'checkbox',
                'options' => null,
                'required' => false,
                'order' => 3,
            ],
            [
                'question_id' => 'chest_pain_nitroglycerin',
                'series_id' => 'cardiac_symptoms',
                'text' => 'درد قفسه سینه که با استراحت یا نیتروگلیسرین بهبود پیدا می‌کند',
                'type' => 'checkbox',
                'options' => null,
                'required' => false,
                'order' => 4,
            ],
            [
                'question_id' => 'chest_pain_food',
                'series_id' => 'cardiac_symptoms',
                'text' => 'درد قفسه سینه که به دنبال مصرف غذاهای چرب یا سنگین ایجاد می‌شود',
                'type' => 'checkbox',
                'options' => null,
                'required' => false,
                'order' => 5,
            ],
            [
                'question_id' => 'chest_pain_breathing',
                'series_id' => 'cardiac_symptoms',
                'text' => 'درد قفسه سینه که هنگام تنفس عمیق یا تغییر وضعیت بدن بدتر می‌شود',
                'type' => 'checkbox',
                'options' => null,
                'required' => false,
                'order' => 6,
            ],
            [
                'question_id' => 'shortness_breath_activity',
                'series_id' => 'cardiac_symptoms',
                'text' => 'تنگی نفس هنگام فعالیت فیزیکی (مثل بالا رفتن از پله‌ها)',
                'type' => 'checkbox',
                'options' => null,
                'required' => false,
                'order' => 7,
            ],
            [
                'question_id' => 'shortness_breath_rest',
                'series_id' => 'cardiac_symptoms',
                'text' => 'تنگی نفس حتی در حالت استراحت یا هنگام دراز کشیدن',
                'type' => 'checkbox',
                'options' => null,
                'required' => false,
                'order' => 8,
            ],
            [
                'question_id' => 'shortness_breath_night',
                'series_id' => 'cardiac_symptoms',
                'text' => 'احساس خفگی یا تنگی نفس ناگهانی در شب (از خواب بیدار می‌شوم و نفس کم می‌آورم)',
                'type' => 'checkbox',
                'options' => null,
                'required' => false,
                'order' => 9,
            ],
            [
                'question_id' => 'swelling',
                'series_id' => 'cardiac_symptoms',
                'text' => 'تنگی نفس همراه با ورم پاها یا افزایش وزن ناگهانی',
                'type' => 'checkbox',
                'options' => null,
                'required' => false,
                'order' => 10,
            ],
            [
                'question_id' => 'dizziness',
                'series_id' => 'cardiac_symptoms',
                'text' => 'سرگیجه یا احساس سبکی سر، مخصوصاً هنگام ایستادن سریع',
                'type' => 'checkbox',
                'options' => null,
                'required' => false,
                'order' => 11,
            ],
            [
                'question_id' => 'fainting',
                'series_id' => 'cardiac_symptoms',
                'text' => 'غش یا بیهوش شدن ناگهانی بدون دلیل مشخص',
                'type' => 'checkbox',
                'options' => null,
                'required' => false,
                'order' => 12,
            ],
            [
                'question_id' => 'fatigue',
                'series_id' => 'cardiac_symptoms',
                'text' => 'احساس ضعف یا خستگی شدید بدون دلیل واضح',
                'type' => 'checkbox',
                'options' => null,
                'required' => false,
                'order' => 13,
            ],
            [
                'question_id' => 'sweating',
                'series_id' => 'cardiac_symptoms',
                'text' => 'تعریق زیاد بدون علت مشخص (خصوصاً همراه با درد قفسه سینه)',
                'type' => 'checkbox',
                'options' => null,
                'required' => false,
                'order' => 14,
            ],
            [
                'question_id' => 'nausea',
                'series_id' => 'cardiac_symptoms',
                'text' => 'تهوع یا استفراغ همراه با احساس ناراحتی در قفسه سینه',
                'type' => 'checkbox',
                'options' => null,
                'required' => false,
                'order' => 15,
            ],
            [
                'question_id' => 'abdominal_pain',
                'series_id' => 'cardiac_symptoms',
                'text' => 'درد یا ورم شکم که با فعالیت بدنی بدتر می‌شود',
                'type' => 'checkbox',
                'options' => null,
                'required' => false,
                'order' => 16,
            ],
            [
                'question_id' => 'cough',
                'series_id' => 'cardiac_symptoms',
                'text' => 'سرفه مزمن یا خس‌خس سینه بدون دلیل مشخص',
                'type' => 'checkbox',
                'options' => null,
                'required' => false,
                'order' => 17,
            ],
            [
                'question_id' => 'none_symptoms',
                'series_id' => 'cardiac_symptoms',
                'text' => 'هیچکدام از علائم بالا را ندارم.',
                'type' => 'checkbox',
                'options' => null,
                'required' => false,
                'order' => 18,
            ],
        ];

        // Insert cardiac symptoms questions
        DB::table('assessment_questions')->insert($questions);

        $this->command->info('Cardiac symptoms questions seeded successfully!');
    }
}
