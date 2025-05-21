<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class FinancialSetting extends Settings
{
    public float $blood_pressure_price;
    public float $online_visit_price;

    public static function group(): string
    {
        return 'financial';
    }
} 