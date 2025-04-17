<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\OnlineVisit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class OnlineVisitController extends Controller
{
    /**
     * Store a new online visit request.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'visit_type' => 'required|in:medical_questions,document_review,prescription_renewal',
            'description' => 'nullable|string',
            'voice_note' => 'nullable|file|mimes:wav,mp3,webm|max:10240', // 10MB max
            'medical_documents' => 'nullable|array',
            // 'medical_documents.*' => 'required|file|mimes:jpg,jpeg,png,pdf|max:10240', // 10MB max per file
            'medical_documents.*' => 'required|file|max:10240', // 10MB max per file

        ]);

        if ($validator->fails()) {
            Log::error('Online visit validation failed', [
                'errors' => $validator->errors(),
                'request' => $request->all()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $data = $validator->validated();
            $documentPaths = [];
            $voiceNoteMetadata = null;

            // Handle voice note upload if present
            if ($request->hasFile('voice_note')) {
                $voiceNote = $request->file('voice_note');
                $voiceNotePath = $voiceNote->store('voice-notes', 'public');

                $voiceNoteMetadata = [
                    'path' => $voiceNotePath,
                    'original_name' => $voiceNote->getClientOriginalName(),
                    'mime_type' => $voiceNote->getMimeType(),
                    'size' => $voiceNote->getSize(),
                    'extension' => $voiceNote->getClientOriginalExtension()
                ];
            }

            // Handle medical documents upload if present
            if ($request->hasFile('medical_documents')) {
                $files = $request->file('medical_documents');
                if (!is_array($files)) {
                    $files = [$files];
                }

                foreach ($files as $file) {
                    try {
                        $path = $file->store('medical-documents', 'public');

                        $documentPaths[] = [
                            'path' => $path,
                            'original_name' => $file->getClientOriginalName(),
                            'mime_type' => $file->getMimeType(),
                            'size' => $file->getSize(),
                            'extension' => $file->getClientOriginalExtension()
                        ];
                    } catch (\Exception $e) {
                        Log::error('Failed to store medical document', [
                            'error' => $e->getMessage(),
                            'file' => $file->getClientOriginalName()
                        ]);
                        throw $e;
                    }
                }
            }

            // Create online visit record
            $onlineVisit = OnlineVisit::create([
                'user_id' => Auth::id(),
                'visit_type' => $data['visit_type'],
                'description' => $data['description'] ?? null,
                'voice_note_path' => $voiceNoteMetadata ,
                'medical_documents' => $documentPaths,
                'status' => 'pending'
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Online visit request submitted successfully',
                'data' => $onlineVisit
            ], 201);

        } catch (\Exception $e) {
            Log::error('Failed to create online visit', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);

            // Clean up any uploaded files if there was an error
            if (isset($voiceNoteMetadata)) {
                Storage::disk('public')->delete($voiceNoteMetadata['path']);
            }
            foreach ($documentPaths as $doc) {
                Storage::disk('public')->delete($doc['path']);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create online visit request',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user's online visits
     */
    public function index()
    {
        $visits = OnlineVisit::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json([
            'status' => 'success',
            'data' => $visits
        ]);
    }
}
