<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use App\Http\Requests\User\ChangeEmialRequest;
use Symfony\Component\HttpFoundation\Response;
use Exception;
use App\Http\Requests\User\ChangePasswordRequest;
use App\Http\Requests\User\ChangeEmailSubmitRequest;

class UserController extends Controller
{
    const CHANGE_EMAIL_CACHE_KEY = 'change.email.for.user.';
    /**
     * تغییر ایمیل کاربر
     */
    public function changeEmail(ChangeEmialRequest $request)
    {
        try {
            $email = $request->email;
            $userId = auth()->id();
            $code = random_verification_code();
            $expiration = now()->addMinutes(config('auth.change_email_cache_expiration', 1440));

            //TODO: Send email and sms code
            Log::info('SEND-CHANGE-EMAIL-CODE', compact('code'));

            Cache::put(self::CHANGE_EMAIL_CACHE_KEY . $userId, compact('email', 'code'), $expiration);

            return response([
                'message' => 'ایمیلی به شما ارسال شد لطفا صندوق دریافتی خود را بررسی نمایید',
            ], Response::HTTP_OK);

        } catch (Exception $exception) {
            Log::info($expiration);

            return response([
                'message' => 'خطایی رخ داده است و سرور قادر به ارسال کد فعالسازی نمیباشد',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * تایید تغییر ایمیل کاربر
     */
    public function changeEmailSubmit(ChangeEmailSubmitRequest $request)
    {
        $userId = auth()->id();
        $cacheKey = self::CHANGE_EMAIL_CACHE_KEY.$userId;
        $cache = Cache::get($cacheKey);
        if(empty($cache) || $cache['code'] != $request->code){
            return response(['message'=>'درخواست شما نامعتبر می باشد.'], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $user = auth()->user();
        $user->email = $cache['email'];
        $user->save();
        Cache::forget($cacheKey);

        return response([
            'message'=>'ایمیل با موفقیت تغییر یافت'
        ], Response::HTTP_ACCEPTED);
    }

    /**
     * تغییر رمز عبور
     */
    public function changePassword(ChangePasswordRequest $request)
    {
        try {
            $user = auth()->user();

            if(!Hash::check($request->oldPassword, $user->password)){
                return response(['message'=>'گذر واژه وارد شده مطابقت ندارد.'], Response::HTTP_UNPROCESSABLE_ENTITY);
            }
            $user->password = bcrypt($request->newPassword);
            $user->save();

            return response(['message'=>'تغییر گذر واژه با موفقیت انجام شد'], Response::HTTP_ACCEPTED);
        }catch (Exception $exception){
            Log::info($exception);
            return response(['message'=>'خطایی در سمت سرور رخ داده است.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

    }
}
