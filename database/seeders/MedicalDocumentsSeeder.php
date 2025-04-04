<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MedicalDocumentsSeeder extends Seeder
{
    /**
     * Run the database seeds for medical documents questions.
     *
     * @return void
     */
    public function run()
    {
        // Define the medical documents questions
        $questions = [
            [
                'question_id' => 'blood_test',
                'series_id' => 'medical_documents',
                'text' => 'نتایج آخرین آزمایش خون (شامل قند خون، چربی خون، عملکرد کلیه و کبد، هموگلوبین A1C در صورت دیابت)',
                'type' => 'checkbox',
                'options' => null,
                'required' => false,
                'order' => 1,
            ],
            [
                'question_id' => 'ecg',
                'series_id' => 'medical_documents',
                'text' => 'نوار قلب (EKG) اخیر (در ۳ روز گذشته)',
                'type' => 'checkbox',
                'options' => null,
                'required' => false,
                'order' => 2,
            ],
            [
                'question_id' => 'echo',
                'series_id' => 'medical_documents',
                'text' => 'اکوکاردیوگرافی (Echo) اخیر (در سه ماه گذشته یا طبق توصیه پزشک)',
                'type' => 'checkbox',
                'options' => null,
                'required' => false,
                'order' => 3,
            ],
            [
                'question_id' => 'ett',
                'series_id' => 'medical_documents',
                'text' => 'تست ورزش ETT (در صورت انجام قبلی)',
                'type' => 'checkbox',
                'options' => null,
                'required' => false,
                'order' => 4,
            ],
            [
                'question_id' => 'nuclear_scan',
                'series_id' => 'medical_documents',
                'text' => 'اسکن هسته ای (در صورت انجام قبلی)',
                'type' => 'checkbox',
                'options' => null,
                'required' => false,
                'order' => 5,
            ],
            [
                'question_id' => 'angiography',
                'series_id' => 'medical_documents',
                'text' => 'نتایج آنژیوگرافی قلب (در صورت داشتن سابقه بیماری عروق کرونر)',
                'type' => 'checkbox',
                'options' => null,
                'required' => false,
                'order' => 6,
            ],
            [
                'question_id' => 'ct_angiography',
                'series_id' => 'medical_documents',
                'text' => 'نتایج سی تی آنژیوگرافی قلب (در صورت داشتن سابقه بیماری عروق کرونر)',
                'type' => 'checkbox',
                'options' => null,
                'required' => false,
                'order' => 7,
            ],
            [
                'question_id' => 'stent_report',
                'series_id' => 'medical_documents',
                'text' => 'گزارش استنت یا فنرگذاری (در صورت انجام عمل استنت‌گذاری)',
                'type' => 'checkbox',
                'options' => null,
                'required' => false,
                'order' => 8,
            ],
            [
                'question_id' => 'bypass_report',
                'series_id' => 'medical_documents',
                'text' => 'گزارش عمل قلب باز (در صورت انجام عمل بای‌پس)',
                'type' => 'checkbox',
                'options' => null,
                'required' => false,
                'order' => 9,
            ],
            [
                'question_id' => 'valve_report',
                'series_id' => 'medical_documents',
                'text' => 'گزارش تعویض یا ترمیم دریچه قلب (در صورت داشتن جراحی دریچه‌ای)',
                'type' => 'checkbox',
                'options' => null,
                'required' => false,
                'order' => 10,
            ],
            [
                'question_id' => 'holter',
                'series_id' => 'medical_documents',
                'text' => 'نتایج بررسی ریتم قلب (هولتر مانیتورینگ ۲۴ تا ۴۸ ساعته) (در صورت داشتن آریتمی یا تپش قلب)',
                'type' => 'checkbox',
                'options' => null,
                'required' => false,
                'order' => 11,
            ],
            [
                'question_id' => 'abpm',
                'series_id' => 'medical_documents',
                'text' => 'نتایج تست فشار خون ۲۴ ساعته ABPM (برای بررسی فشار خون ناپایدار یا فشار خون مقاوم به درمان)',
                'type' => 'checkbox',
                'options' => null,
                'required' => false,
                'order' => 12,
            ],
            [
                'question_id' => 'carotid',
                'series_id' => 'medical_documents',
                'text' => 'گزارش سونوگرافی عروق گردن (کاروتید داپلر) اخیر (برای بررسی تنگی عروق مغزی)',
                'type' => 'checkbox',
                'options' => null,
                'required' => false,
                'order' => 13,
            ],
            [
                'question_id' => 'dvt',
                'series_id' => 'medical_documents',
                'text' => 'گزارش سونوگرافی داپلر وریدی پا (در صورت داشتن سابقه لخته خون در پا یا واریس شدید)',
                'type' => 'checkbox',
                'options' => null,
                'required' => false,
                'order' => 14,
            ],
            [
                'question_id' => 'risk_assessment',
                'series_id' => 'medical_documents',
                'text' => 'غربالگری قلبی و عروقی (ریسک سنجی بیماری قلبی)',
                'type' => 'checkbox',
                'options' => null,
                'required' => false,
                'order' => 15,
            ],
            [
                'question_id' => 'none_docs',
                'series_id' => 'medical_documents',
                'text' => 'هیچکدام از این مدارک را ندارم.',
                'type' => 'checkbox',
                'options' => null,
                'required' => false,
                'order' => 16,
            ],
        ];

        // Insert medical documents questions
        DB::table('assessment_questions')->insert($questions);

        $this->command->info('Medical documents questions seeded successfully!');
    }
}
