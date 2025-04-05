<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrationPath = database_path('settings/prompt-settings.json');
        $this->migrator->add('prompts.documents_prompt', 'لطفا عکس ها و تصاویر پزشکی را خیلی دقیق تحلیل و آنالیز کن و نکات آن را به انگلیسی برای من دقیق بنویس');
        $this->migrator->add('prompts.assessment_prompt', "Please write a comprehensive analysis of the patient's personal information and assessment answers. Include:
        1. Patient introduction and personal information analysis
        2. Risk factors identification
        3. Lifestyle status evaluation
        4. Symptom analysis (typical vs atypical in percentages)
        5. Pretest probability of CAD
        6. Differential diagnoses
        7. Economic status assessment and willingness for diagnostic tests
        Please write this analysis in English, but keep patient's personal information in Original language.");
        $this->migrator->add('prompts.full_prompt', "Please write me a long professional report with information (about patient introduction and personal information), risk factors, lifestyle status, how many percent of the symptoms are typical and how many percent are atypical, write in percentage and separate analysis, what is the pretest probability of CAD in this patient. Write about differential diagnoses. Write the patient's economic status and say if he is willing to do diagnostic tests. Write a report of the analysis of medical documents and images, for example, history, echocardiography, CT angiography, angiography and other documents along with the date of the report and list of druge. Write this text in English");
    }

};

