<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TagController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VideoController;
use App\Http\Controllers\ChannelController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\PlaylistController;
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
Route::match(['post', 'put'], 'change-password', [UserController::class, 'changePassword'])->name('user.change-password');
});

/**
 * Route Group for channel
 */
Route::group(['middleware'=>['auth:api'], 'prefix'=>'/channel'], function (){
    Route::match(['post', 'put'],'upload', [ChannelController::class, 'uploadAvatarForChannel'])->name('channel.upload-avatar');
    Route::match(['post', 'put'],'socials', [ChannelController::class, 'updateSocials'])->name('channel.update-socials');
    Route::put('{id?}', [ChannelController::class, 'updateChannelInfo'])->name('channel.update');

});

/**
 * Rout Group for Video
 */
Route::group(['middleware'=>[], 'prefix'=>'/video'], function (){
    Route::post('/{video}/like', [VideoController::class, 'like'])->name('video.like');
    Route::get('/', [VideoController::class, 'index'])->name('video.list-videos');

    Route::group(['middleware'=>['auth:api']], function (){
        Route::post('/upload', [VideoController::class, 'uploadVideo'])->name('video.upload');
        Route::post('/upload-banner', [VideoController::class, 'uploadBanner'])->name('video.upload-banner');
        Route::post('/', [VideoController::class, 'createVideo'])->name('video.create');
        Route::put('/{video}/state', [VideoController::class, 'changeState'])->name('video.change-state');
        Route::post('/{video}/republish',[VideoController::class, 'republish'])->name('video.republish');
    });
});

/**
 * Rout Group for Category
 */
Route::group(['middleware'=>['auth:api'], 'prefix'=>'/category'], function (){
    Route::get('/', [CategoryController::class, 'getAllCategories'])->name('category.all');
    Route::get('/my', [CategoryController::class, 'getMyCategories'])->name('category.my');
    Route::post('/upload-banner', [CategoryController::class, 'uploadBanner'])->name('category.upload-banner');
    Route::post('/', [CategoryController::class, 'create'])->name('category.create');
});

/**
 * Rout Group for Playlist
 */
Route::group(['middleware'=>['auth:api'], 'prefix'=>'/playlist'], function (){
    Route::get('/', [PlaylistController::class, 'getAllPlaylist'])->name('playlist.all');
    Route::get('/my', [PlaylistController::class, 'getMyPlaylist'])->name('playlist.my');
    Route::post('/create', [PlaylistController::class, 'create'])->name('playlist.create');
});

/**
 * Rout Group for Tag
 */
Route::group(['middleware'=>['auth:api'], 'prefix'=>'/tag'], function (){
    Route::get('/', [TagController::class, 'index'])->name('playlist.all');
    Route::post('/create', [TagController::class, 'create'])->name('playlist.create');
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
