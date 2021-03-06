<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use App\Http\Requests\User\ChangeEmialRequest;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Requests\User\FollowersUserRequest;
use App\Http\Requests\User\ChangePasswordRequest;
use App\Http\Requests\User\FollowingsUserRequest;
use App\Http\Requests\User\UnregisterUserRequest;
use App\Http\Requests\User\UnFollowChannelRequest;
use App\Http\Requests\Channel\FollowChannelRequest;
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

    public function follow(FollowChannelRequest $request)
    {
        $user = $request->user();
        $user->follow($request->channel->user);

        return response(['message'=>'با موفقیت انجام شد'], Response::HTTP_CREATED);
    }

    public function unFollow(UnFollowChannelRequest $request)
    {
        $user = $request->user();
        $user->unFollow($request->channel->user);

        return response(['message'=>'با موفقیت انجام شد'], Response::HTTP_OK);
    }

    public function followings(FollowingsUserRequest $request)
    {
        return $request->user()->followings()->paginate();
    }

    public function followers(FollowersUserRequest $request)
    {
        return $request->user()->followers()->paginate();
    }

    public function unRegister(UnregisterUserRequest $request)
    {
        try {
            DB::beginTransaction();
            $request->user()->delete();
            DB::commit();

            return response(['message'=>'کاربر با موفقیت غیر فعال شد.برای فعال شدن نیاز به ورود مجدد می باشد.'], Response::HTTP_OK);
        }catch (Exception $exception){
            DB::rollBack();
            Log::error($exception);

            return response(['message'=>'غیرفعال شدن کاربر با شکست مواجه شد.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
