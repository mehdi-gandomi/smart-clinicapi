<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AssessmentQuestion;
use Illuminate\Support\Facades\DB;

class MedicationAdherenceSeeder extends Seeder
{
    /**
     * Run the database seeds for medication adherence questions.
     *
     * @return void
     */
    public function run()
    {
        // Define the medication adherence questions
        $questions = [
            [
                'question_id' => 'full_adherence',
                'series_id' => 'medication_adherence',
                'text' => 'تمام داروهای تجویز شده را طبق دستور پزشک و بدون قطع یا تغییر مصرف می‌کنم',
                'type' => 'checkbox',
                'options' => null,
                'required' => false,
                'order' => 1,
            ],
            [
                'question_id' => 'occasional_forgetfulness',
                'series_id' => 'medication_adherence',
                'text' => 'داروهای تجویز شده را مصرف می‌کنم اما گاهی اوقات برخی دزها را فراموش می‌کنم',
                'type' => 'checkbox',
                'options' => null,
                'required' => false,
                'order' => 2,
            ],
            [
                'question_id' => 'discontinue_ineffective',
                'series_id' => 'medication_adherence',
                'text' => 'اگر احساس کنم که داروها تأثیری ندارند یا عوارضی ایجاد می‌کنند، بدون مشورت با پزشک مصرف را قطع می‌کنم',
                'type' => 'checkbox',
                'options' => null,
                'required' => false,
                'order' => 3,
            ],
            [
                'question_id' => 'side_effects_concern',
                'series_id' => 'medication_adherence',
                'text' => 'گاهی اوقات داروهای تجویز شده را مصرف نمی‌کنم زیرا نگران عوارض جانبی آن‌ها هستم',
                'type' => 'checkbox',
                'options' => null,
                'required' => false,
                'order' => 4,
            ],
            [
                'question_id' => 'natural_preference',
                'series_id' => 'medication_adherence',
                'text' => 'تمایلی به مصرف مداوم دارو ندارم و ترجیح می‌دهم به روش‌های طبیعی تکیه کنم',
                'type' => 'checkbox',
                'options' => null,
                'required' => false,
                'order' => 5,
            ],
            [
                'question_id' => 'lifestyle_changes',
                'series_id' => 'medication_adherence',
                'text' => 'اگر پزشک توصیه کند که تغییراتی در رژیم غذایی، کاهش وزن یا افزایش فعالیت بدنی داشته باشم، آن‌ها را رعایت می‌کنم',
                'type' => 'checkbox',
                'options' => null,
                'required' => false,
                'order' => 6,
            ],
            [
                'question_id' => 'healthier_lifestyle',
                'series_id' => 'medication_adherence',
                'text' => 'تمایل دارم سبک زندگی سالم‌تری داشته باشم، اما رعایت رژیم غذایی و ورزش برایم سخت است',
                'type' => 'checkbox',
                'options' => null,
                'required' => false,
                'order' => 7,
            ],
            [
                'question_id' => 'lifestyle_difficulty',
                'series_id' => 'medication_adherence',
                'text' => 'توصیه‌های مربوط به رژیم غذایی و ورزش را به‌طور کامل رعایت نمی‌کنم، زیرا تغییر سبک زندگی برایم دشوار است',
                'type' => 'checkbox',
                'options' => null,
                'required' => false,
                'order' => 8,
            ],
            [
                'question_id' => 'no_changes',
                'series_id' => 'medication_adherence',
                'text' => 'در حال حاضر تمایلی به تغییر سبک زندگی یا مصرف داروها ندارم',
                'type' => 'checkbox',
                'options' => null,
                'required' => false,
                'order' => 9,
            ],
        ];

        // Insert medication adherence questions
        DB::table('assessment_questions')->insert($questions);

        $this->command->info('Medication adherence questions seeded successfully!');
    }
}
