<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Cookie;
use GuzzleHttp\Client;
use Illuminate\Support\Str;

class GoogleController extends Controller
{
    public function redirect()
    {
        // Get doctor name from request
        $doctorName = request()->query('doctor', 'ghasemi');

        // Generate a random state
        $state = Str::random(40);
        
        // Store both state and doctor name in session
        session([
            'oauth_state' => $state,
            'doctor_name' => $doctorName
        ]);

        // Configure Socialite to skip SSL verification (development only)
        $clientConfig = [
            'verify' => false,
            'curl' => [
                CURLOPT_SSL_VERIFYPEER => false
            ]
        ];

        return Socialite::driver('google')
            ->setHttpClient(new Client($clientConfig))
            ->with(['state' => $state])
            ->redirect();
    }

    public function callback()
    {
        try {
            // Configure Socialite to skip SSL verification (development only)
            $clientConfig = [
                'verify' => false,
                'curl' => [
                    CURLOPT_SSL_VERIFYPEER => false
                ]
            ];

            $googleUser = Socialite::driver('google')
                ->setHttpClient(new Client($clientConfig))
                ->stateless()
                ->user();

            if (!$googleUser || !$googleUser->getId()) {
                \Log::error('Google auth failed: No user data received');
                return redirect(env('FRONTEND_URL', 'http://localhost:3000') . '/login?error=failed_to_get_user_data');
            }

            // Get doctor name from session
            $doctorName = session('doctor_name', 'ghasemi');

            // Log successful Google data retrieval
            \Log::info('Google user data received', [
                'google_id' => $googleUser->getId(),
                'email' => $googleUser->getEmail(),
                'doctor_name' => $doctorName
            ]);

            try {
                // Split the Google name into first and last name
                $nameParts = explode(' ', $googleUser->getName() ?? 'Google User');
                $firstName = $nameParts[0] ?? 'Google';
                $lastName = $nameParts[1] ?? 'User';

                $user = User::updateOrCreate(
                    ['google_id' => $googleUser->getId()],
                    [
                        'first_name' => $firstName,
                        'last_name' => $lastName,
                        'name' => $googleUser->getName() ?? 'Google User',
                        'email' => $googleUser->getEmail(),
                        'google_id' => $googleUser->getId(),
                        'avatar' => $googleUser->getAvatar(),
                        'user_type' => 'user',
                        'doctor' => $doctorName,
                    ]
                );

                // Log successful user creation/update
                \Log::info('User created/updated successfully', ['user_id' => $user->id]);

                $token = $user->createToken('auth-token')->plainTextToken;

                // Encode the token for URL safety
                $encodedToken = urlencode($token);

                // Clear the OAuth state from session
                session()->forget(['oauth_state', 'doctor_name']);

                // Redirect to doctor-specific callback URL
                return redirect(env('FRONTEND_URL') . "/{$doctorName}/auth/callback?token=" . $encodedToken);

            } catch (\Exception $e) {
                dd($e);
                // Log database or token creation errors
                \Log::error('Error creating/updating user or token', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);

                return redirect(env('FRONTEND_URL') . "/{$doctorName}/login?error=" . urlencode('Error creating user account'));
            }

        } catch (\Exception $e) {
            dd($e);
            // Get doctor name from session
            $doctorName = session('doctor_name', 'ghasemi');

            // Log the detailed error
            \Log::error('Google authentication failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'doctor_name' => $doctorName
            ]);

            $errorMessage = env('APP_DEBUG')
                ? urlencode($e->getMessage())
                : urlencode('Authentication failed. Please try again.');

            return redirect(env('FRONTEND_URL') . "/{$doctorName}/login?error=" . $errorMessage);
        }
    }
}
