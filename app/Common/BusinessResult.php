<?php

declare(strict_types=1);

namespace App\Common;

use App\POJO\BusinessResponse;
use App\POJO\VO\BaseObject;

class BusinessResult
{
    private function __construct()
    {
    }

    /**
     * 返回结果成功
     *
     * @param mixed $data 响应数据
     * @return BusinessResponse
     */
    public static function ok(mixed $data): BusinessResponse
    {
        $businessResult = new BusinessResponse();

        return $businessResult->ok($data);
    }

    /**
     * 根据枚举返回错误信息
     *
     * @param BaseObject $baseObject 基础返回对象
     * @return BusinessResponse
     */
    public static function failForEnum(BaseObject $baseObject): BusinessResponse
    {
        $businessResult = new BusinessResponse();

        return $businessResult->fail($baseObject->code, $baseObject->message);
    }

    /**
     * 返回结果失败
     *
     * @param int $code 响应编码
     * @param string $message 响应信息
     * @param mixed|null $data 响应数据
     * @return BusinessResponse
     */
    public static function fail(int $code = 50000, string $message = 'service exception', mixed $data = null): BusinessResponse
    {
        $businessResult = new BusinessResponse();

        return $businessResult->fail($code, $message, $data);
    }
}