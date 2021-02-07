<?php
use Illuminate\Support\Facades\Log;
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

if (! function_exists('uniqueId')) {
    function uniqueId(int $value)
    {
        $hash = new \Hashids\Hashids(env('APP_KEY'), 10);

        return $hash->encode($value);
    }
}

if (! function_exists('clear_storage')) {
    function clear_storage($storageName)
    {
        try
        {
            Storage::disk($storageName)->delete(Storage::disk($storageName)->allFiles());
            foreach (Storage::disk($storageName)->allDirectories() as $directory){
                Storage::disk($storageName)->deleteDirectory($directory);
            }
            return true;
        }catch (Exception $exception){
            Log::info($exception);
            return false;
        }
    }
}



