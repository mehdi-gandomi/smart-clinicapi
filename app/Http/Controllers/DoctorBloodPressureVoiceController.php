<?php

namespace App\Http\Controllers;

use App\Models\DoctorBloodPressureVoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DoctorBloodPressureVoiceController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'voice' => 'required|file|mimes:webm|max:10240', // Max 10MB
            'blood_pressure_id' => 'required|exists:blood_pressures,id'
        ]);

        $voiceFile = $request->file('voice');
        $path = $voiceFile->store('doctor-voices', 'public');

        DoctorBloodPressureVoice::create([
            'user_id' => auth()->id(),
            'blood_pressure_id' => $request->blood_pressure_id,
            'voice_path' => $path
        ]);

        return response()->json(['message' => 'Voice note saved successfully']);
    }
} 