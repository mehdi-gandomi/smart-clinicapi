<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UserMedicalDoc;
use Illuminate\Support\Facades\Storage;

class UserMedicalDocController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'doc_type' => 'required|in:blood_test,other',
            'description' => 'sometimes',
            'user_assessment_id' => 'sometimes',
            'files.*' => 'required|file|image|max:10240', // 10MB max for each file
        ]);

        try {
            $files = [];

            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $file) {
                    $path = $file->store('medical-docs', 'public');

                    $files[] = [
                        'path' => $path,
                        'original_name' => $file->getClientOriginalName(),
                        'mime_type' => $file->getMimeType(),
                        'size' => $file->getSize(),
                    ];
                }
            }

            $doc = UserMedicalDoc::create([
                'user_id' => auth()->id(),
                'doc_type' => $request->doc_type,
                'user_assessment_id' => $request->user_assessment_id,
                'description' => $request->description,
                'files' => $files
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Medical documents saved successfully',
                'data' => $doc
            ]);

        } catch (\Exception $e) {
            // Delete uploaded files if there was an error
            if (!empty($files)) {
                foreach ($files as $file) {
                    Storage::disk('public')->delete($file['path']);
                }
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to save medical documents',
                'error' => $e->getMessage()
            ]);
        }
    }
}
