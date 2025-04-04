<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UserMedicalDoc;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class MedicalDocController extends Controller
{
    public function index(Request $request)
    {
        try {
            $perPage = $request->input('per_page', 10);
            $page = $request->input('page', 1);

            $query = UserMedicalDoc::where('user_id', Auth::id())
                ->orderBy('created_at', 'desc');

            $total = $query->count();
            $docs = $query->skip(($page - 1) * $perPage)
                         ->take($perPage)
                         ->get();

            return response()->json([
                'status' => 'success',
                'data' => $docs,
                'total' => $total,
                'current_page' => (int)$page,
                'per_page' => (int)$perPage,
                'last_page' => ceil($total / $perPage)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch medical documents'
            ], 500);
        }
    }

    public function download($path)
    {
        try {
            // Decode the path if it's URL encoded
            $path = urldecode($path);

            // Security check: ensure the file exists and is within the medical-docs directory
            if (!Storage::disk('public')->exists($path) || !str_starts_with($path, 'medical-docs/')) {
                throw new \Exception('File not found or access denied');
            }

            // Get the file
            $file = Storage::disk('public')->path($path);

            // Get the original file name from the path
            $originalName = basename($path);

            // Return the file download response
            return response()->download($file, $originalName);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to download file: ' . $e->getMessage()
            ], 404);
        }
    }
}
