<?php

declare(strict_types=1);


namespace App\Utils;

use App\Constants\CommonConstant;

class Stopwatch
{
    /**
     * @var string|float 开始时间
     */
    private string|float $startTime;

    public function __construct()
    {
        $this->startTime = microtime(true);
    }

    /**
     * 计算耗时
     *
     * @param int $desiredUnit 计算单位 1为分 3为毫秒 其他为秒
     * @return string|float
     */
    public function elapsed(int $desiredUnit = CommonConstant::ZERO): string|float
    {
        $timeConsumption = microtime(true) - $this->startTime;
        return match ($desiredUnit) {
            CommonConstant::ONE => round($timeConsumption / 60, CommonConstant::THREE),
            CommonConstant::THREE => round($timeConsumption * 1000, CommonConstant::THREE),
            default => $timeConsumption,
        };
    }
}