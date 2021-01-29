<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use App\Exceptions\RegisterVerificationException;
use App\Http\Requests\Auth\RegisterNewUserRequest;
use App\Exceptions\UserAlreadyRegisteredException;
use App\Http\Requests\Auth\RegisterVerifyUserRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Requests\Auth\ResendVerificationCodeRequest;

class AuthController extends Controller
{
    /*
     * ثبت نام کاربر
     */
    public function register(RegisterNewUserRequest $request)
    {
        try {
            DB::beginTransaction();
            $field = $request->getFieldName();
            $value = $request->getFieldValue();

            if ($field === 'mobile') {
                $value = to_valid_mobile_number($request->input($field));
            }

            if ($user = User::where($field, $value)->first()) {
                if (! empty($user->verified_at)) {
                    throw new UserAlreadyRegisteredException('شما قبلا ثبت نام کرده اید.');
                }

                return response(['message' => 'کد فعال سازی قبلا برای شما ارسال شده است.'], Response::HTTP_OK);
            }

            $code = random_int(100000, 999999);
            $user = User::create([
                $field => $value,
                'verify_code' => $code,
            ]);

            //TODO: send sms or email
            Log::info('SEND_REGISTER_CODE_MESSAGE_TO_USER', ['code' => $code]);
            DB::commit();

            return response(['message' => 'کاربر ثبت موقت شد.'], Response::HTTP_OK);
        } catch (\Exception $exception) {
            DB::rollBack();

            if($exception instanceof UserAlreadyRegisteredException){
                throw $exception;
            }
            Log::info($exception);

            return response([
                'message' => 'خطایی در سمت سرور رخ داده است.',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /*
     * تایید کد فعال سازی
     */
    public function registerVerify(RegisterVerifyUserRequest $request)
    {
        $field = $request->getFieldName();
        $value = $request->getFieldValue();
        $code = request()->code;

        if ($field === 'mobile') {
            $value = to_valid_mobile_number($request->input($field));
        }

        $user = User::where([
            $field => $value,
            'verify_code' => $code,
        ])->first();

        if (empty($user)) {
            throw new ModelNotFoundException('کاربری با کد مورد نظر یافت نشد.');
        }

        $user->verify_code = null;
        $user->verified_at = now();
        $user->save();

        return response($user, Response::HTTP_OK);
    }

    /*
     * ارسال محدد کد فعال سازی به کاربر
     */
    public function resendVerificationCode(ResendVerificationCodeRequest $request)
    {
        $field = $request->getFieldName();
        $value = $request->getFieldValue();

        if ($field === 'mobile') {
            $value = to_valid_mobile_number($request->input($field));
        }

        $user = User::where($field, $value)->whereNull('verified_at')->first();

        if (! empty($user)) {
            //اگر زمان مورد نظر از ارسال کد قبلی گذشته بود از کد جدید ارسال میکنیم
            $dateDiff = now()->diffInMinutes($user->updated_at);
            if ($dateDiff > config('auth.resend_verification_code_time_diff', 60)) {
                $user->verify_code = random_verification_code();
                $user->save();
            }

            //TODO: send sms or email
            Log::info('SEND_REGISTER_CODE_MESSAGE_TO_USER', ['code' => $user->verify_code]);

            return response([
                'message' => 'کد فعال سازی مجددا ارسال شد.',
            ], Response::HTTP_OK);
        }

        throw  new ModelNotFoundException('کاربری با این مشخصات یافت نشد یا قبلا فعال سازی شده است');
    }
}
