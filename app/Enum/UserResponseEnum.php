<?php

declare(strict_types=1);


namespace App\Enum;

use App\Common\BusinessResult;
use App\POJO\BusinessResponse;

enum UserResponseEnum
{
    case ERROR_LOGIN_TYPE;
    case AUTOMATIC_LOGIN_CIPHERTEXT_IS_EMPTY;
    case ACCOUNT_IS_EMPTY;
    case ERROR_LOGIN_ACCOUNT_TYPE;
    case PASSWORD_IS_EMPTY;
    case CAPTCHA_IS_EMPTY;

    public function response(): BusinessResponse
    {
        return match ($this) {
            self::ERROR_LOGIN_TYPE => BusinessResult::fail(50001, '登录类型错误！'),
            self::AUTOMATIC_LOGIN_CIPHERTEXT_IS_EMPTY => BusinessResult::fail(50002, '自动登录密文不能为空！'),
            self::ACCOUNT_IS_EMPTY => BusinessResult::fail(50003, '登录账号不能为空！'),
            self::ERROR_LOGIN_ACCOUNT_TYPE => BusinessResult::fail(50004, '错误账号登录类型！'),
            self::PASSWORD_IS_EMPTY => BusinessResult::fail(50005, '登录密码不能为空！'),
            self::CAPTCHA_IS_EMPTY => BusinessResult::fail(50006, '验证码不能为空！'),
        };
    }
}
