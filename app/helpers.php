<?php

/**
 * اضافه کردن +98 به ابتدای شماره موبایل
 */
if (! function_exists('to_valid_mobile_number')) {
    function to_valid_mobile_number($mobile)
    {
        return '+98' . substr($mobile, -10, 10);
    }
}

/*
 * ایجاد کد فعال سازی تصادفی برای ثبت نام
 */
if (! function_exists('random_verification_code')) {
    function random_verification_code()
    {
        return random_int(100000, 999999);
    }
}


