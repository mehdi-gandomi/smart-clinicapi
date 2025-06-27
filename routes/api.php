<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\AssessmentController;
use App\Http\Controllers\Api\AssessmentSeriesController;
use App\Http\Controllers\Api\UserDrugController;
use App\Http\Controllers\Api\UserMedicalDocController;
use App\Http\Controllers\Api\UserAssessmentAdditionalInfoController;
use App\Http\Controllers\Api\MedicalDocController;
use App\Http\Controllers\Api\OnlineVisitController;
use App\Http\Controllers\Api\WalletController;
use App\Models\Wallet;
use App\Http\Controllers\Api\DoctorController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Public routes
Route::prefix('auth')->group(function () {
    Route::post('check-mobile', [AuthController::class, 'checkMobile']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('send-otp', [AuthController::class, 'sendOtp']);
    Route::post('verify-otp', [AuthController::class, 'verifyOtp']);
    Route::post('register', [AuthController::class, 'register']);
});

Route::middleware(['auth:sanctum', 'throttle:60,1'])->group(function () {
    Route::get('/user', function (Request $request) {
        // return $request->user();
        $user = $request->user();
         if(!$user->wallet){
            Wallet::create(['user_id' => $user->id]);
         }
        $user->load('wallet');
        return response()->json($user);
    });

    Route::put('/user/profile', [ProfileController::class, 'update']);

    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/auth/check', [AuthController::class, 'check']);

    // Assessment routes
    Route::get('/assessments', [AssessmentController::class, 'index']);
    Route::get('/assessments/current', [AssessmentController::class, 'getOrCreate']);
    Route::get('/assessments/{id}', [AssessmentController::class, 'show']);
    Route::post('/assessments/answers', [AssessmentController::class, 'saveAnswers']);
    Route::post('/assessments/documents', [AssessmentController::class, 'uploadDocument']);
    Route::post('/assessments/complete', [AssessmentController::class, 'complete']);
    Route::get('/assessments/questions/{section}', [AssessmentController::class, 'getQuestions']);

    Route::get('/assessment/series', [AssessmentSeriesController::class, 'index']);
    Route::get('/assessment/series-with-questions', [AssessmentController::class, 'getSeriesWithQuestions']);
    Route::post('/assessment/save-answers', [AssessmentController::class, 'saveAnswers']);
    Route::post('/assessment/ask-gpt', [AssessmentController::class, 'askGpt']);

    // Drug routes
    Route::apiResource('drugs', UserDrugController::class);

    Route::post('medical-docs', [UserMedicalDocController::class, 'store']);

    Route::post('assessment/additional-info', [UserAssessmentAdditionalInfoController::class, 'store']);

    Route::get('/medical-docs', [MedicalDocController::class, 'index']);
    Route::get('/medical-docs/download/{path}', [MedicalDocController::class, 'download'])
        ->where('path', '.*'); // Allow slashes in the path parameter

    // Online Visits
    Route::post('/online-visits', [OnlineVisitController::class, 'store']);
    Route::get('/online-visits', [OnlineVisitController::class, 'index']);
    Route::delete('/online-visits/{onlineVisit}', [OnlineVisitController::class, 'destroy']);

    // Wallet routes
    Route::get('/wallet', [WalletController::class, 'show']);
    Route::get('/wallet/transactions', [WalletController::class, 'transactions']);
    Route::post('/wallet/deposit', [WalletController::class, 'deposit']);
    Route::post('/wallet/charge', [WalletController::class, 'charge']);

    Route::post('/wallet/withdraw', [WalletController::class, 'withdraw']);

    Route::middleware('auth:sanctum')->post('/blood-pressure', [\App\Http\Controllers\Api\BloodPressureController::class, 'store']);

    // Financial Settings
    Route::get('/financial/prices', [App\Http\Controllers\Api\FinancialController::class, 'getPrices']);


    // Blood Pressure Voice Recordings
    Route::get('/blood-pressure/voices', [App\Http\Controllers\Api\BloodPressureController::class, 'getVoiceRecordings']);
    Route::get('/blood-pressure/data', [App\Http\Controllers\Api\BloodPressureController::class, 'getBloodPressureData']);

    
});
Route::get('/doctors/{slug}', [DoctorController::class, 'getBySlug']);
Route::any('/wallet/charge/callback/{transaction}/{doctor}', [WalletController::class, 'chargeCallback'])->name('wallet.charge.callback');
