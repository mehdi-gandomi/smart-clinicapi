<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Settings\FinancialSetting;
use Illuminate\Http\JsonResponse;

class FinancialController extends Controller
{
    public function getPrices(): JsonResponse
    {
        $settings = app(FinancialSetting::class);

        return response()->json([
            'blood_pressure_price' => $settings->blood_pressure_price,
            'online_visit_price' => $settings->online_visit_price,
        ]);
    }
} 