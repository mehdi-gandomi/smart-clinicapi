<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AssessmentSeriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Clear existing series
        DB::table('assessment_series')->truncate();

        // Define the 9 series based on your form titles (removed the duplicate 10th one)
        $seriesList = [
            [
                'series_id' => 'cardiac_risk_factors',
                'title' => '۱. کدام یک از فاکتورهای زیر در مورد شما صادق می‌کند؟ (می‌توانید چند گزینه انتخاب کنید.)',
                'description' => 'فاکتورهای خطر قلبی و عروقی',
                'order' => 1,
            ],
            [
                'series_id' => 'medical_documents',
                'title' => '۲. لطفاً مشخص کنید کدام یک از مدارک زیر را به همراه دارید. (می‌توانید چند گزینه انتخاب کنید.)',
                'description' => 'مدارک پزشکی',
                'order' => 2,
            ],
            [
                'series_id' => 'cardiac_symptoms',
                'title' => '۳. کدام یک از علائم زیر را در چند هفته اخیر یا در چند ماه اخیر تجربه کرده‌اید؟ (می‌توانید چند گزینه انتخاب کنید.)',
                'description' => 'علائم قلبی',
                'order' => 3,
            ],
            [
                'series_id' => 'physical_activity',
                'title' => '۴. میزان فعالیت بدنی شما چگونه بوده است؟ (می‌توانید چند گزینه انتخاب کنید.)',
                'description' => 'فعالیت فیزیکی',
                'order' => 4,
            ],
            [
                'series_id' => 'dietary_habits',
                'title' => '۵. کدام یک از گزینه‌های زیر بیشتر به عادات غذایی شما نزدیک‌تر است؟ (می‌توانید چند گزینه انتخاب کنید.)',
                'description' => 'عادات غذایی',
                'order' => 5,
            ],
            [
                'series_id' => 'consultation_reasons',
                'title' => '۶. دلایل اصلی مراجعه به کلینیک تخصصی قلب و عروق را انتخاب نمایید؟ (می‌توانید چند گزینه انتخاب کنید.)',
                'description' => 'دلایل مراجعه',
                'order' => 6,
            ],
            [
                'series_id' => 'diagnostic_preferences',
                'title' => '۷. برای اطمینان از وضعیت سلامت قلب خود، چند مورد از گزینه‌های زیر مهم‌تر است؟ (می‌توانید چند گزینه انتخاب کنید.)',
                'description' => 'ترجیحات تشخیصی',
                'order' => 7,
            ],
            [
                'series_id' => 'medication_adherence',
                'title' => '۸. در صورت دریافت نسخه دارویی یا توصیه‌های پزشکی، کدام گزینه بیشتر به شرایط شما نزدیک است؟ (می‌توانید چند گزینه انتخاب کنید.)',
                'description' => 'پایبندی به درمان',
                'order' => 8,
            ],
            [
                'series_id' => 'stress_and_sleep',
                'title' => '۹. در ماه‌های اخیر، میزان استرس و وضعیت خواب شما چگونه بوده است؟ (می‌توانید چند گزینه انتخاب کنید.)',
                'description' => 'استرس و خواب',
                'order' => 9,
            ],
        ];

        // Insert all series
        DB::table('assessment_series')->insert($seriesList);

        $this->command->info('Assessment series seeded successfully!');
    }
}
