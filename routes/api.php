<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ChannelController;
use Laravel\Passport\Http\Controllers\AccessTokenController;

/**
 * *Route Group for login and register
 **/
Route::group([], function ($route){
Route::post('login', [AccessTokenController::class, 'issueToken'])->name('auth.login')->middleware('throttle');
Route::post('register', [AuthController::class, 'register'])->name('auth.register');
Route::post('register-verify', [AuthController::class, 'registerVerify'])->name('auth.register-verify');
Route::post('resend-verification-code', [AuthController::class, 'resendVerificationCode'])->name('auth.resend-verification-code');
});

/**
 * Route group for user
 */
Route::group(['middleware'=>['auth:api']], function (){
Route::post('change-email',[UserController::class, 'changeEmail'])->name('user.change-email');
Route::post('change-email-submit',[UserController::class, 'changeEmailSubmit'])->name('user.change-email-submit');
});

/**
 * Route Group for channel
 */
Route::group(['middleware'=>['auth:api'], 'prefix'=>'/channel'], function (){
    Route::put('{id?}', [ChannelController::class, 'updateChannelInfo'])->name('channel.update');
});


//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});
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
