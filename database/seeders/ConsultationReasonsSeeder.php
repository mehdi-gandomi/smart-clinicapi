<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AssessmentQuestion;
use Illuminate\Support\Facades\DB;

class ConsultationReasonsSeeder extends Seeder
{
    /**
     * Run the database seeds for consultation reasons questions.
     *
     * @return void
     */
    public function run()
    {
        // Define the consultation reasons questions
        $questions = [
            [
                'question_id' => 'routine_checkup',
                'series_id' => 'consultation_reasons',
                'text' => 'چکاپ و بررسی سلامت قلب در کلینیک چکاپ ویژه (ارزیابی دوره‌ای بدون علائم خاص)',
                'type' => 'checkbox',
                'options' => null,
                'required' => false,
                'order' => 1,
            ],
            [
                'question_id' => 'imaging_review',
                'series_id' => 'consultation_reasons',
                'text' => 'مشاهده و تحلیل سی‌تی تصویربرداری قلب (آنژیو، سی‌تی آنژیو، MRI قلب)',
                'type' => 'checkbox',
                'options' => null,
                'required' => false,
                'order' => 2,
            ],
            [
                'question_id' => 'post_procedure',
                'series_id' => 'consultation_reasons',
                'text' => 'بررسی قلبی و عروقی بعد از انجام عمل آنژیوگرافی و آنژیوپلاستی',
                'type' => 'checkbox',
                'options' => null,
                'required' => false,
                'order' => 3,
            ],
            [
                'question_id' => 'medication_review',
                'series_id' => 'consultation_reasons',
                'text' => 'بررسی عوارض احتمالی داروهای قلبی یا نیاز به تغییر آن‌ها',
                'type' => 'checkbox',
                'options' => null,
                'required' => false,
                'order' => 4,
            ],
            [
                'question_id' => 'invasive_treatment',
                'series_id' => 'consultation_reasons',
                'text' => 'بررسی نیاز به درمان‌های تهاجمی مانند آنژیوگرافی، جراحی یا ابلیشن قلبی',
                'type' => 'checkbox',
                'options' => null,
                'required' => false,
                'order' => 5,
            ],
            [
                'question_id' => 'symptoms_evaluation',
                'series_id' => 'consultation_reasons',
                'text' => 'بررسی قلبی به سبب علائم قلبی و عروقی (نظیر: درد قفسه سینه، تنگی نفس، تپش قلب، سرگیجه و ...)',
                'type' => 'checkbox',
                'options' => null,
                'required' => false,
                'order' => 6,
            ],
            [
                'question_id' => 'test_interpretation',
                'series_id' => 'consultation_reasons',
                'text' => 'بررسی و تفسیر نتایج آزمایشات و تست‌های قلبی (نوار قلب، اکو، تست ورزش، سی‌تی آنژیوگرافی و سایر مدارک پزشکی)',
                'type' => 'checkbox',
                'options' => null,
                'required' => false,
                'order' => 7,
            ],
            [
                'question_id' => 'blood_pressure',
                'series_id' => 'consultation_reasons',
                'text' => 'بررسی مشکلات مربوط با فشار خون (کنترل فشار خون ناپایدار)',
                'type' => 'checkbox',
                'options' => null,
                'required' => false,
                'order' => 8,
            ],
            [
                'question_id' => 'diabetes_heart',
                'series_id' => 'consultation_reasons',
                'text' => 'کنترل و درمان دیابت و تأثیر آن بر قلب (بررسی عوارض قلبی در بیماران دیابتی)',
                'type' => 'checkbox',
                'options' => null,
                'required' => false,
                'order' => 9,
            ],
            [
                'question_id' => 'surgery_clearance',
                'series_id' => 'consultation_reasons',
                'text' => 'دریافت نامه رضایت برای انجام جراحی‌های غیر قلبی (ارزیابی ریسک عمل از نظر وضعیت قلبی و صدور تأییدیه پزشکی)',
                'type' => 'checkbox',
                'options' => null,
                'required' => false,
                'order' => 10,
            ],
        ];

        // Insert consultation reasons questions
        DB::table('assessment_questions')->insert($questions);

        $this->command->info('Consultation reasons questions seeded successfully!');
    }
}
