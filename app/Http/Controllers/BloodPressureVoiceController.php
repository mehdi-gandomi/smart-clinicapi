<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Storage;
use App\Models\DoctorBloodPressureVoice;
use App\Models\BloodPressure;
class BloodPressureVoiceController extends Controller
{
    public function upload(Request $request)
    {
        try {
            if ($request->hasFile('voice')) {
                $file = $request->file('voice');
                $filename = uniqid('voice_') . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('public/voices', $filename);
                $path=str_replace("public/","",$path);
                BloodPressure::whereIn("id",explode(",",$request->blood_pressure_ids))->update([
                    'examined'=>1
                ]);

                // ذخیره رکورد در دیتابیس
                DoctorBloodPressureVoice::create([
                    'user_id'     => $request->input('user_id'),
                    'doctor_id'   => auth()->id(),
                    'voice_path'=>$path,
                    'blood_pressure_ids'=>$request->blood_pressure_ids
                ]);

                return response()->json(['success' => true, 'path' => Storage::url($path)]);
            }

            return response()->json(['success' => false, 'message' => 'فایل ارسال نشده است']);
        } catch (\Exception $e) {

            \Log::error('Voice upload error', ['message' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'خطا در ذخیره فایل']);
        }
    }
}
