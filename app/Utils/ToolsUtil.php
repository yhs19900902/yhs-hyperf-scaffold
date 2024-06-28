<?php

declare(strict_types=1);


namespace App\Utils;

use App\Constants\CommonConstant;
use Hyperf\Stringable\Str;

class ToolsUtil
{
    /**
     * 获取32位uuid
     *
     * @return string
     */
    public static function shortUuid(): string
    {
        return Str::replace(CommonConstant::DASH, CommonConstant::EMPTY, self::uuid());
    }

    /**
     * 获取uuid
     *
     * @return string
     */
    public static function uuid(): string
    {
        return Str::uuid()->toString();
    }

    private function ____construct()
    {

    }
}