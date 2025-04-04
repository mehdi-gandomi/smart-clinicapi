<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    /**
     * Update the user's profile.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'first_name' => 'sometimes|string|max:255',
            'last_name' => 'sometimes|string|max:255',
            'name' => 'sometimes|string|max:255',
            'national_id' => 'sometimes|string|max:255',
            'gender' => 'sometimes',
            'age' => 'sometimes|integer|nullable',
            'height' => 'sometimes|integer|nullable',
            'weight' => 'sometimes|integer|nullable',
            'primary_insurance' => 'sometimes',
            'supplementary_insurance' => 'sometimes',
            'occupation' => 'sometimes',
            'address' => 'sometimes'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user->first_name = $request->input('first_name');
        $user->last_name = $request->input('last_name');
        $user->name = $request->input('name');
        $user->national_id = $request->input('national_id');
        $user->gender = $request->input('gender');
        $user->age = $request->input('age');
        $user->height = $request->input('height');
        $user->weight = $request->input('weight');
        $user->primary_insurance = $request->input('primary_insurance');
        $user->supplementary_insurance = $request->input('supplementary_insurance');
        $user->occupation = $request->input('occupation');
        $user->address = $request->input('address');

        $user->save();

        return response()->json(['message' => 'Profile updated successfully']);
    }
}
