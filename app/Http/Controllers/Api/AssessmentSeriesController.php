<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AssessmentSeries;
use App\Http\Resources\AssessmentSeriesResource;
use Illuminate\Http\JsonResponse;

class AssessmentSeriesController extends Controller
{
    /**
     * Get all assessment series with their questions
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $series = AssessmentSeries::with(['questions' => function ($query) {
            $query->orderBy('order');
        }])
        ->orderBy('order')
        ->get();

        return response()->json([
            'status' => 'success',
            'data' => AssessmentSeriesResource::collection($series)
        ]);
    }
}
