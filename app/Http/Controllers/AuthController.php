<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;
use App\Exceptions\RegisterVerificationException;
use App\Http\Requests\Auth\RegisterNewUserRequest;
use App\Exceptions\UserAlreadyRegisteredException;
use App\Http\Requests\Auth\RegisterVerifyUserRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class AuthController extends Controller
{
    /*
     * ثبت نام کاربر
     */
    public function register(RegisterNewUserRequest $request)
    {
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
        User::create([
            $field => $value,
            'verify_code' => $code,
        ]);

        Log::info('SEND_REGISTER_CODE_MESSAGE_TO_USER', ['code' => $code]);

        return response(['message' => 'کاربر ثبت موقت شد.'], Response::HTTP_OK);
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
}
