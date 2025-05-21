<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\GoogleController;
use App\Jobs\ProcessGptAssessment;
use App\Models\User;
use App\Models\UserAssessment;
use Illuminate\Support\Facades\Http;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    

    

    return view('welcome');
})->name('home');

Route::prefix('auth')->group(function () {
    Route::get('google', [AuthController::class, 'redirectToGoogle']);
    Route::get('google/callback', [AuthController::class, 'handleGoogleCallback']);
});

Route::get('auth/google', [GoogleController::class, 'redirect'])
    ->name('google.redirect');

Route::get('auth/google/callback', [GoogleController::class, 'callback']);
Route::get('dispatch/{id}', function($id){
    $assessment=UserAssessment::find($id);
    ProcessGptAssessment::dispatch($assessment,User::first());
});
