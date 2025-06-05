<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BloodPressure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Models\DoctorBloodPressureVoice;

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

    public function getVoiceRecordings(Request $request)
    {
        $user = $request->user();
        
        $voices = DoctorBloodPressureVoice::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($voice) {
                return [
                    'id' => $voice->id,
                    'voice_path' => asset('storage/' . $voice->voice_path),
                    'created_at' => $voice->created_at->format('Y-m-d H:i:s'),
                    'blood_pressure_ids' => $voice->blood_pressure_ids
                ];
            });

        return response()->json([
            'status' => 'success',
            'data' => $voices
        ]);
    }

    public function getBloodPressureData(Request $request)
    {
        $ids = explode(',', $request->ids);
        
        $bloodPressures = BloodPressure::whereIn('id', $ids)
            ->orderBy('date', 'desc')
            ->get()
            ->map(function ($bp) {
                return [
                    'id' => $bp->id,
                    'date' => $bp->date,
                    'systolic' => $bp->systolic,
                    'diastolic' => $bp->diastolic,
                    'heart_rate' => $bp->heart_rate,
                    'notes' => $bp->notes
                ];
            });

        return response()->json([
            'status' => 'success',
            'data' => $bloodPressures
        ]);
    }
}
