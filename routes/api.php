<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use Laravel\Passport\Http\Controllers\AccessTokenController;

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
// Route Group for authenticated user only
Route::group([], function ($route){
     Route::post('login', [AccessTokenController::class, 'issueToken'])->name('auth.login')->middleware('throttle');
     Route::post('register', [AuthController::class, 'register'])->name('auth.register');
     Route::post('register-verify', [AuthController::class, 'registerVerify'])->name('auth.register-verify');
     Route::post('resend-verification-code', [AuthController::class, 'resendVerificationCode'])->name('auth.resend-verification-code');
});

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
