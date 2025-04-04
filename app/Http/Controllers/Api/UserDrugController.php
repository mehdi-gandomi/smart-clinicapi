<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UserDrug;
use Illuminate\Support\Facades\Storage;

class UserDrugController extends Controller
{
    /**
     * Store drug information with files
     */
    public function store(Request $request)
    {
        $request->validate([
            'description' => 'sometimes',
            'user_assessment_id' => 'sometimes',
            'files.*' => 'required|file|image|max:10240', // 10MB max for each file
        ]);

        try {
            $files = [];

            // Handle file uploads
            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $file) {
                    $path = $file->store('drug-images', 'public');

                    // Store file information
                    $files[] = [
                        'path' => $path,
                        'original_name' => $file->getClientOriginalName(),
                        'mime_type' => $file->getMimeType(),
                        'size' => $file->getSize(),
                    ];
                }
            }

            // Create drug record with files
            $drug = UserDrug::create([
                'user_id' => auth()->id(),
                'description' => $request->description,
                'user_assessment_id' => $request->user_assessment_id,
                'files' => $files
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Drug information saved successfully',
                'data' => $drug
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
                'message' => 'Failed to save drug information',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user's drugs
     */
    public function index()
    {
        $drugs = UserDrug::where('user_id', auth()->id())
            ->latest()
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $drugs
        ]);
    }

    /**
     * Get a specific drug record
     */
    public function show($id)
    {
        try {
            $drug = UserDrug::where('user_id', auth()->id())
                ->findOrFail($id);

            return response()->json([
                'status' => 'success',
                'data' => $drug
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Drug record not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Update a drug record
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'description' => 'required|string',
            'files.*' => 'nullable|file|image|max:10240',
        ]);

        try {
            $drug = UserDrug::where('user_id', auth()->id())
                ->findOrFail($id);

            $files = $drug->files ?? [];

            // Handle new file uploads
            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $file) {
                    $path = $file->store('drug-images', 'public');

                    // Store file information
                    $files[] = [
                        'path' => $path,
                        'original_name' => $file->getClientOriginalName(),
                        'mime_type' => $file->getMimeType(),
                        'size' => $file->getSize(),
                    ];
                }
            }

            $drug->update([
                'description' => $request->description,
                'files' => $files
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Drug information updated successfully',
                'data' => $drug
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update drug information',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a drug record
     */
    public function destroy($id)
    {
        try {
            $drug = UserDrug::where('user_id', auth()->id())
                ->findOrFail($id);

            // Delete associated files from storage
            if (!empty($drug->files)) {
                foreach ($drug->files as $file) {
                    Storage::disk('public')->delete($file['path']);
                }
            }

            $drug->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Drug record deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete drug record',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
