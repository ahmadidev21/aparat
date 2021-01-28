<?php

/**
 * اضافه کردن +98 به ابتدای شماره موبایل
 */
function to_valid_mobile_number($mobile)
{
    return '+98' . substr($mobile, -10, 10);
}