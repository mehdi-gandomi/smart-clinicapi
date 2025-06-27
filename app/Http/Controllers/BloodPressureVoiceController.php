<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Storage;
use App\Models\DoctorBloodPressureVoice;
use App\Models\BloodPressure;
use App\Models\User;
use App\Settings\FinancialSetting;
class BloodPressureVoiceController extends Controller
{
    public function upload(Request $request)
    {
        try {
            $settings = app(FinancialSetting::class);
            $user=User::find($request->input('user_id'));
            if ($request->hasFile('voice')) {
                $file = $request->file('voice');
                $filename = uniqid('voice_') . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('public/voices', $filename);
                $path=str_replace("public/","",$path);
                BloodPressure::whereIn("id",explode(",",$request->blood_pressure_ids))->update([
                    'examined'=>1
                ]);

                
    

                // ذخیره رکورد در دیتابیس
                $voice=DoctorBloodPressureVoice::create([
                    'user_id'     => $request->input('user_id'),
                    'doctor_id'   => auth()->id(),
                    'voice_path'=>$path,
                    'blood_pressure_ids'=>$request->blood_pressure_ids
                ]);
        // Create a pending wallet transaction
        $transaction = WalletTransaction::create([
            'user_id' => $user->id,
            'type'=>'debit',
            'amount' => $settings->blood_pressure_price,
            'description' => $request->description ?? 'شارژ کیف پول',
            'status' => 'completed',
            'metadata'=>[
                'doctor_blood_pressure_voice_id'=>$voice->id,
                'transaction_type'=>'doctor blood pressure voice'
            ],
            'transaction_id' => Str::random(32), // Generate a unique transaction ID
        ]);
        $user->decrement($settings->blood_pressure_price);
                return response()->json(['success' => true, 'path' => Storage::url($path)]);
            }else{
                BloodPressure::whereIn("id",explode(",",$request->blood_pressure_ids))->update([
                    'examined'=>1
                ]);

                // ذخیره رکورد در دیتابیس
                $voice=DoctorBloodPressureVoice::create([
                    'user_id'     => $request->input('user_id'),
                    'doctor_id'   => auth()->id(),
                    'notes'=>$request->notes,
                    'blood_pressure_ids'=>$request->blood_pressure_ids
                ]);
    $transaction = WalletTransaction::create([
                'user_id' => $user->id,
                'type'=>'debit',
                'amount' => $settings->blood_pressure_price,
                'description' => $request->description ?? 'شارژ کیف پول',
                'status' => 'completed',
                'metadata'=>[
                'doctor_blood_pressure_voice_id'=>$voice->id,
                'transaction_type'=>'doctor blood pressure voice'
            ],
                'transaction_id' => Str::random(32), // Generate a unique transaction ID
            ]);
            $user->decrement($settings->blood_pressure_price);
                return response()->json(['success' => true]);
            }

            // return response()->json(['success' => false, 'message' => 'فایل ارسال نشده است']);
        } catch (\Exception $e) {

            \Log::error('Voice upload error', ['message' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'خطا در ذخیره فایل']);
        }
    }
}
