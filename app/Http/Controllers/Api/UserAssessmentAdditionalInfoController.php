<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessGptAssessment;
use App\Models\User;
use App\Models\UserAssessment;
use Illuminate\Http\Request;
use App\Models\UserAssessmentAdditionalInfo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class UserAssessmentAdditionalInfoController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'user_assessment_id' => 'sometimes|exists:user_assessments,id',
            'text_description' => 'nullable|string',
            'voice_file' => 'nullable|file|mimes:webm,mp3,wav|max:20480', // 20MB max
            'voice_duration' => 'nullable|string',
        ]);

        try {
            $data = [
                'user_assessment_id' => $request->user_assessment_id,
                'text_description' => $request->text_description,
                'voice_duration' => $request->voice_duration,
            ];

            if ($request->hasFile('voice_file')) {
                $path = $request->file('voice_file')->store('assessment-voices', 'public');
                $data['voice_path'] = $path;
            }

            $additionalInfo = UserAssessmentAdditionalInfo::create($data);
            if($request->user_assessment_id){
                $assessment=UserAssessment::find($request->user_assessment_id);
                $user = User::find(Auth::id());
                ProcessGptAssessment::dispatch($assessment, $user);
            }
            return response()->json([
                'status' => 'success',
                'message' => 'Additional information saved successfully',
                'data' => $additionalInfo
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to save additional information',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
