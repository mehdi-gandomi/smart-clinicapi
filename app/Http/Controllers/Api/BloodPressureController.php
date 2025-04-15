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
        $validator = Validator::make($request->all(), [
            'date' => 'required|date_format:Y-m-d',
            'time' => 'required|date_format:H:i',
            'systolic' => 'required|integer|min:0',
            'diastolic' => 'required|integer|min:0',
            'heart_rate' => 'required|integer|min:0',
            'notes' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        $user = Auth::user();
        $validatedData = $validator->validated();

        try {
            $bloodPressure = BloodPressure::create([
                'user_id' => $user->id,
                'date' => $validatedData['date'],
                'time' => $validatedData['time'],
                'systolic' => $validatedData['systolic'],
                'diastolic' => $validatedData['diastolic'],
                'heart_rate' => $validatedData['heart_rate'],
                'notes' => $validatedData['notes'],
            ]);

            return response()->json(['status' => 'success', 'message' => 'Blood pressure recorded successfully', 'data' => $bloodPressure], 201);

        } catch (\Exception $e) {
            Log::error('Error saving blood pressure: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'An error occurred while saving the data.'], 500);
        }
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
