<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BloodPressure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class BloodPressureController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(['status' => 'error', 'message' => 'Not implemented'], 501);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|string',
            'systolic' => 'required|integer',
            'diastolic' => 'required|integer',
            'heart_rate' => 'required|integer',
            'notes' => 'nullable|string',
        ]);

        $user = Auth::user();

        $bp = BloodPressure::create([
            'user_id' => $user->id,
            'date' => verta()->parse($request->date)->formatGregorian("Y-m-d H:i"),
            'systolic' => $request->systolic,
            'diastolic' => $request->diastolic,
            'heart_rate' => $request->heart_rate,
            'notes' => $request->notes,
        ]);

        return response()->json([
            'status' => 'success',
            'data' => $bp,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return response()->json(['status' => 'error', 'message' => 'Not implemented'], 501);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        return response()->json(['status' => 'error', 'message' => 'Not implemented'], 501);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        return response()->json(['status' => 'error', 'message' => 'Not implemented'], 501);
    }
}
