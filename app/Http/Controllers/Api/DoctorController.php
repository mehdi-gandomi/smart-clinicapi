<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use Illuminate\Http\Request;

class DoctorController extends Controller
{
    /**
     * Get doctor data by slug.
     */
    public function getBySlug($slug)
    {
        $doctor = Doctor::where('slug', $slug)
            ->with(['user:id,name,email,avatar'])
            ->first();

        if (!$doctor) {
            return response()->json([
                'message' => 'Doctor not found'
            ], 404);
        }

        return response()->json([
            'data' => [
                'id' => $doctor->id,
                'name' => $doctor->name,
                'slug' => $doctor->slug,
                'title' => $doctor->title,
                'subtitle' => $doctor->subtitle,
                'panel_type' => $doctor->panel_type,
                'user' => [
                    'id' => $doctor->user->id,
                    'name' => $doctor->user->name,
                    'email' => $doctor->user->email,
                    'avatar' => $doctor->user->avatar,
                ]
            ]
        ]);
    }
} 