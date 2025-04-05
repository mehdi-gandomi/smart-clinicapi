<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class PromptSettings extends Settings
{
    public string $documents_prompt;
    public string $assessment_prompt;
    public string $full_prompt;

    public static function group(): string
    {
        return 'prompts';
    }
}
