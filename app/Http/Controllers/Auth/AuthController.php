<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Otp;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cookie;

class AuthController extends Controller
{
    public function checkMobile(Request $request)
    {
        $request->validate([
            'mobile' => 'required|string|size:11'
        ]);

        $exists = User::where('mobile', $request->mobile)->exists();

        return response()->json([
            'exists' => $exists
        ]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'mobile' => 'required|string|size:11',
            'password' => 'required|string'
        ]);

        $user = User::where('mobile', $request->mobile)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Invalid credentials'
            ], 401);
        }

        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => $user
        ]);
    }

    public function sendOtp(Request $request)
    {
        $request->validate([
            'mobile' => 'required|string|size:11'
        ]);

        // Generate OTP
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Store OTP
        Otp::updateOrCreate(
            ['mobile' => $request->mobile],
            [
                'otp' => Hash::make($otp),
                'expires_at' => Carbon::now()->addMinutes(5)
            ]
        );

        // In production, send OTP via SMS service
        // For development, return OTP in response
        return response()->json([
            'message' => 'OTP sent successfully',
            'otp' => $otp // Remove this in production
        ]);
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'mobile' => 'required|string|size:11',
            'otp' => 'required|string|size:6'
        ]);

        $otpRecord = Otp::where('mobile', $request->mobile)
            ->where('verified', false)
            ->where('expires_at', '>', Carbon::now())
            ->first();

        if (!$otpRecord) {
            return response()->json([
                'message' => 'OTP not found or expired'
            ], 400);
        }

        if (!Hash::check($request->otp, $otpRecord->otp)) {
            return response()->json([
                'message' => 'Invalid OTP'
            ], 400);
        }

        // Mark OTP as verified
        $otpRecord->update(['verified' => true]);

        // Check if user exists
        $user = User::where('mobile', $request->mobile)->first();

        if ($user) {
            $token = $user->createToken('auth-token')->plainTextToken;
            return response()->json([
                'isNewUser' => false,
                'token' => $token,
                'user' => $user
            ]);
        }

        return response()->json([
            'isNewUser' => true
        ]);
    }

    public function register(Request $request)
    {
        $request->validate([
            'mobile' => 'required|string|size:11',
            'name' => 'required|string|max:255'
        ]);

        // Create user
        $user = User::create([
            'mobile' => $request->mobile,
            'name' => $request->name
        ]);

        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'message' => 'User registered successfully',
            'token' => $token,
            'user' => $user
        ]);
    }

    public function redirectToGoogle()
    {
        try {
            return Socialite::driver('google')
                ->with([
                    'prompt' => 'select_account',
                    'access_type' => 'offline',
                    'response_type' => 'code'
                ])
                ->redirect();
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Google authentication initialization failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            if (!$googleUser || !$googleUser->getId()) {
                return redirect(env('FRONTEND_URL', 'http://localhost:3000/login') . '?error=failed_to_get_user_data');
            }

            // Create or update user
            $user = User::updateOrCreate(
                ['google_id' => $googleUser->getId()],
                [
                    'name' => $googleUser->getName() ?? 'Google User',
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar()
                ]
            );

            // Generate token
            $token = $user->createToken('auth-token')->plainTextToken;

            // Redirect with cookie
            return redirect(env('FRONTEND_URL', 'http://localhost:3000/assessment'))
                ->withCookie(Cookie::make(
                    'auth_token',
                    $token,
                    60 * 24 * 30, // 30 days
                    '/',
                    parse_url(env('FRONTEND_URL', 'http://localhost:3000'), PHP_URL_HOST),
                    env('APP_ENV') === 'production', // secure
                    true, // httpOnly
                    false,
                    'Lax' // sameSite
                ));

        } catch (\Exception $e) {
            return redirect(env('FRONTEND_URL', 'http://localhost:3000/login') .
                '?error=' . urlencode('Authentication failed'));
        }
    }

    public function user(Request $request)
    {
        try {
            $user = $request->user();

            if (!$user) {
                return response()->json(['error' => 'User not found'], 401);
            }

            return response()->json($user);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Authentication failed'], 401);
        }
    }

    public function logout(Request $request)
    {
        try {
            $request->user()->currentAccessToken()->delete();

            return response()->json([
                'message' => 'Successfully logged out'
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Logout failed'], 500);
        }
    }

    public function check(Request $request)
    {
        return response()->json([
            'authenticated' => auth()->check(),
            'user' => auth()->user()
        ]);
    }
}
