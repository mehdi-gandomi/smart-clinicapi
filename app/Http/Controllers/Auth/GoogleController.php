<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Cookie;
use GuzzleHttp\Client;

class GoogleController extends Controller
{
    public function redirect()
    {
        // Configure Socialite to skip SSL verification (development only)
        $clientConfig = [
            'verify' => false,
            'curl' => [
                CURLOPT_SSL_VERIFYPEER => false
            ]
        ];

        return Socialite::driver('google')
            ->setHttpClient(new Client($clientConfig))
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
                ->user();

            if (!$googleUser || !$googleUser->getId()) {
                \Log::error('Google auth failed: No user data received');
                return redirect(env('FRONTEND_URL', 'http://localhost:3000/login') . '?error=failed_to_get_user_data');
            }

            // Log successful Google data retrieval
            \Log::info('Google user data received', [
                'google_id' => $googleUser->getId(),
                'email' => $googleUser->getEmail()
            ]);

            try {
                $user = User::updateOrCreate(
                    ['google_id' => $googleUser->getId()],
                    [
                        'name' => $googleUser->getName() ?? 'Google User',
                        'email' => $googleUser->getEmail(),
                        'google_id' => $googleUser->getId(),
                        'avatar' => $googleUser->getAvatar(),
                        'user_type'=>'user'
                    ]
                );

                // Log successful user creation/update
                \Log::info('User created/updated successfully', ['user_id' => $user->id]);

                $token = $user->createToken('auth-token')->plainTextToken;

                // Encode the token for URL safety
                $encodedToken = urlencode($token);

                // Log successful token creation
                \Log::info('Token created successfully');

                return redirect(env('FRONTEND_URL') . '/auth/callback?token=' . $encodedToken);

            } catch (\Exception $e) {
                // Log database or token creation errors
                \Log::error('Error creating/updating user or token', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);

                return redirect(env('FRONTEND_URL') . '/login?error=' . urlencode('Error creating user account'));
            }

        } catch (\Exception $e) {
            // Log the detailed error
            \Log::error('Google authentication failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            $errorMessage = env('APP_DEBUG')
                ? urlencode($e->getMessage())
                : urlencode('Authentication failed. Please try again.');

            return redirect(env('FRONTEND_URL') . '/login?error=' . $errorMessage);
        }
    }
}
