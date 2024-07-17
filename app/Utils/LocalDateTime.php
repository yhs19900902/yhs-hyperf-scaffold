<?php

declare(strict_types=1);


namespace App\Utils;

use App\Constants\CommonConstant;
use DateInterval;
use DateTime;
use DateTimeZone;
use Exception;

class LocalDateTime
{
    protected const DATE_FORMAT = 'Y-m-d H:i:s';
    protected const DATE_FORMAT_SHORT = 'Y-m-d';
    protected const DATE_FORMAT_TIME = 'H:i:s';
    protected const DATE_FORMAT_FULL = 'Y-m-d H:i:s.u';
    protected const DATE_INTERVAL_SECONDS = 'PT%dS';
    protected const DATE_INTERVAL_MINUTES = 'PT%dM';
    protected const DATE_INTERVAL_HOURS = 'PT%dH';
    protected const DATE_INTERVAL_DAYS = 'P%dD';
    protected const DATE_INTERVAL_WEEKS = 'P%dW';
    protected const DATE_INTERVAL_MONTHS = 'P%dM';
    protected const DATE_INTERVAL_YEARS = 'P%dY';

    private function __construct()
    {
    }

    /**
     * 获取周几
     *
     * @param string|DateTime|null $dateTime
     * @param string $format
     * @return int
     */
    public static function getDayOfWeek(null|string|DateTime $dateTime = null, string $format = self::DATE_FORMAT): int
    {
        $dateTime = self::setDateTime($dateTime, $format);
        return intval($dateTime->format('w'));
    }

    /**
     * 设置时间
     *
     * @param string|DateTime|null $dateTime
     * @param string $format
     * @return DateTime
     */
    private static function setDateTime(null|string|DateTime $dateTime = null, string $format = self::DATE_FORMAT): DateTime
    {
        if (null === $dateTime) {
            return self::now();
        } else if (is_string($dateTime)) {
            return self::toDateTime($dateTime, $format);
        } else {
            return $dateTime;
        }
    }

    /**
     * 获取当前时间
     *
     * @return DateTime
     */
    public static function now(): DateTime
    {
        try {
            $timezone = new DateTimeZone('Asia/Shanghai');
            return new DateTime('now', $timezone);
        } catch (Exception $e) {
            return new DateTime();
        }
    }

    /**
     * 格式化时间转DateTime
     *
     * @param string $dateTimeString 字符串时间
     * @param string $format 格式
     * @return DateTime
     */
    public static function toDateTime(string $dateTimeString, string $format = self::DATE_FORMAT): DateTime
    {
        return DateTime::createFromFormat($format, $dateTimeString);
    }

    /**
     * 获取格式化时间
     *
     * @param DateTime|null $dateTime 时间
     * @param string $format 日期格式
     * @return string
     */
    public static function format(?DateTime $dateTime, string $format = self::DATE_FORMAT): string
    {
        $dateTime = $dateTime ?? self::now();
        return $dateTime->format($format);
    }

    /**
     * 重写时间
     *
     * @param string|DateTime $newDate
     * @param string|DateTime $newTime
     * @return DateTime
     */
    public static function with(string|DateTime $newDate, string|DateTime $newTime): DateTime
    {
        $newDate = $newDate instanceof DateTime ? self::format($newDate, self::DATE_FORMAT_SHORT) : $newDate;
        $newTime = $newTime instanceof DateTime ? self::format($newTime, self::DATE_FORMAT_TIME) : $newTime;
        return self::toDateTime("{$newDate} {$newTime}");
    }

    /**
     * 重写年
     *
     * @param int $year
     * @return DateTime
     */
    public static function withYear(int $year): DateTime
    {
        $month = self::getMonth();
        $day = self::getDay();
        $hour = self::getHour();
        $minute = self::getMinute();
        $second = self::getSecond();
        return self::toDateTime("{$year}-{$month}-{$day} {$hour}:{$minute}:{$second}");
    }

    /**
     * 获取月
     *
     * @param string|DateTime|null $dateTime
     * @param string $format
     * @return int
     */
    public static function getMonth(null|string|DateTime $dateTime = null, string $format = self::DATE_FORMAT): int
    {
        $dateTime = self::setDateTime($dateTime, $format);
        return intval($dateTime->format('m'));
    }

    /**
     * 获取日
     *
     * @param string|DateTime|null $dateTime
     * @param string $format
     * @return int
     */
    public static function getDay(null|string|DateTime $dateTime = null, string $format = self::DATE_FORMAT): int
    {
        $dateTime = self::setDateTime($dateTime, $format);
        return intval($dateTime->format('d'));
    }

    /**
     * 获取时
     *
     * @param string|DateTime|null $dateTime
     * @param string $format
     * @return int
     */
    public static function getHour(null|string|DateTime $dateTime = null, string $format = self::DATE_FORMAT): int
    {
        $dateTime = self::setDateTime($dateTime, $format);
        return intval($dateTime->format('H'));
    }

    /**
     * 获取分
     *
     * @param string|DateTime|null $dateTime
     * @param string $format
     * @return int
     */
    public static function getMinute(null|string|DateTime $dateTime = null, string $format = self::DATE_FORMAT): int
    {
        $dateTime = self::setDateTime($dateTime, $format);
        return intval($dateTime->format('i'));
    }

    /**
     * 获取秒
     *
     * @param string|DateTime|null $dateTime
     * @param string $format
     * @return int
     */
    public static function getSecond(null|string|DateTime $dateTime = null, string $format = self::DATE_FORMAT): int
    {
        $dateTime = self::setDateTime($dateTime, $format);
        return intval($dateTime->format('s'));
    }

    /**
     * 重写月
     *
     * @param int $month
     * @return DateTime
     */
    public static function withMonth(int $month): DateTime
    {
        $year = self::getYear();
        $day = self::getDay();
        $hour = self::getHour();
        $minute = self::getMinute();
        $second = self::getSecond();
        return self::toDateTime("{$year}-{$month}-{$day} {$hour}:{$minute}:{$second}");
    }

    /**
     * 获取年
     *
     * @param string|DateTime|null $dateTime
     * @param string $format
     * @return int
     */
    public static function getYear(null|string|DateTime $dateTime = null, string $format = self::DATE_FORMAT): int
    {
        $dateTime = self::setDateTime($dateTime, $format);
        return intval($dateTime->format('Y'));
    }

    /**
     * 重写天
     *
     * @param int $day
     * @return DateTime
     */
    public static function withDay(int $day): DateTime
    {
        $year = self::getYear();
        $month = self::getMonth();
        $hour = self::getHour();
        $minute = self::getMinute();
        $second = self::getSecond();
        return self::toDateTime("{$year}-{$month}-{$day} {$hour}:{$minute}:{$second}");
    }

    /**
     * 重写时
     *
     * @param int $hour
     * @return DateTime
     */
    public static function withHour(int $hour): DateTime
    {
        $year = self::getYear();
        $month = self::getMonth();
        $day = self::getDay();
        $minute = self::getMinute();
        $second = self::getSecond();
        return self::toDateTime("{$year}-{$month}-{$day} {$hour}:{$minute}:{$second}");
    }

    /**
     * 重写分
     *
     * @param int $minute
     * @return DateTime
     */
    public static function withMinute(int $minute): DateTime
    {
        $year = self::getYear();
        $month = self::getMonth();
        $day = self::getDay();
        $hour = self::getHour();
        $second = self::getSecond();
        return self::toDateTime("{$year}-{$month}-{$day} {$hour}:{$minute}:{$second}");
    }

    /**
     * 重写秒
     *
     * @param int $second
     * @return DateTime
     */
    public static function withSecond(int $second): DateTime
    {
        $year = self::getYear();
        $month = self::getMonth();
        $day = self::getDay();
        $hour = self::getHour();
        $minute = self::getMinute();
        return self::toDateTime("{$year}-{$month}-{$day} {$hour}:{$minute}:{$second}");
    }

    /**
     * 转时间
     *
     * @param int $year
     * @param int $month
     * @param int $day
     * @param int $hour
     * @param int $minute
     * @param int $second
     * @return DateTime
     */
    public static function of(int $year = 1997, int $month = 1, int $day = 1, int $hour = 0, int $minute = 0, int $second = 0): DateTime
    {
        $time = "{$year}-{$month}-{$day} {$hour}:{$minute}:{$second}";
        return self::toDateTime($time);
    }

    /**
     * 添加年
     *
     * @param int $years
     * @param string|DateTime|null $dateTime
     * @return DateTime
     */
    public static function plusYear(int $years, null|string|DateTime $dateTime = null): DateTime
    {
        $dateTime = self::setDateTime($dateTime);
        try {
            return $dateTime->add(new DateInterval(sprintf(self::DATE_INTERVAL_YEARS, $years)));
        } catch (Exception) {
            return $dateTime->modify(sprintf('+%d years', $years));
        }
    }

    /**
     * 添加月
     *
     * @param int $months
     * @param string|DateTime|null $dateTime
     * @return DateTime
     */
    public static function plusMonth(int $months, null|string|DateTime $dateTime = null): DateTime
    {
        $dateTime = self::setDateTime($dateTime);
        try {
            return $dateTime->add(new DateInterval(sprintf(self::DATE_INTERVAL_MONTHS, $months)));
        } catch (Exception) {
            return $dateTime->modify(sprintf('+%d months', $months));
        }
    }

    /**
     * 添加日
     *
     * @param int $days
     * @param string|DateTime|null $dateTime
     * @return DateTime
     */
    public static function plusDay(int $days, null|string|DateTime $dateTime = null): DateTime
    {
        $dateTime = self::setDateTime($dateTime);
        try {
            return $dateTime->add(new DateInterval(sprintf(self::DATE_INTERVAL_DAYS, $days)));
        } catch (Exception) {
            return $dateTime->modify(sprintf('+%d days', $days));
        }
    }

    /**
     * 添加时
     *
     * @param int $hours
     * @param string|DateTime|null $dateTime
     * @return DateTime
     */
    public static function plusHour(int $hours, null|string|DateTime $dateTime = null): DateTime
    {
        $dateTime = self::setDateTime($dateTime);
        try {
            return $dateTime->add(new DateInterval(sprintf(self::DATE_INTERVAL_HOURS, $hours)));
        } catch (Exception) {
            return $dateTime->modify(sprintf('+%d hours', $hours));
        }
    }

    /**
     * 添加分
     *
     * @param int $minutes
     * @param string|DateTime|null $dateTime
     * @return DateTime
     */
    public static function plusMinute(int $minutes, null|string|DateTime $dateTime = null): DateTime
    {
        $dateTime = self::setDateTime($dateTime);
        try {
            return $dateTime->add(new DateInterval(sprintf(self::DATE_INTERVAL_MINUTES, $minutes)));
        } catch (Exception) {
            return $dateTime->modify(sprintf('+%d minutes', $minutes));
        }
    }

    /**
     * 添加秒
     *
     * @param int $seconds
     * @param string|DateTime|null $dateTime
     * @return DateTime
     */
    public static function plusSecond(int $seconds, null|string|DateTime $dateTime = null): DateTime
    {
        $dateTime = self::setDateTime($dateTime);
        try {
            return $dateTime->add(new DateInterval(sprintf(self::DATE_INTERVAL_SECONDS, $seconds)));
        } catch (Exception) {
            return $dateTime->modify(sprintf('+%d seconds', $seconds));
        }
    }

    /**
     * 添加周
     *
     * @param int $weeks
     * @param string|DateTime|null $dateTime
     * @return DateTime
     */
    public static function plusWeeks(int $weeks, null|string|DateTime $dateTime = null): DateTime
    {
        $dateTime = self::setDateTime($dateTime);
        try {
            return $dateTime->add(new DateInterval(sprintf(self::DATE_INTERVAL_WEEKS, $weeks)));
        } catch (Exception) {
            return $dateTime->modify(sprintf('+%d weeks', $weeks));
        }
    }

    /**
     * 减年
     *
     * @param int $years
     * @param string|DateTime|null $dateTime
     * @return DateTime
     */
    public static function minusYears(int $years, null|string|DateTime $dateTime = null): DateTime
    {
        $dateTime = self::setDateTime($dateTime);
        try {
            return $dateTime->sub(new DateInterval(sprintf(self::DATE_INTERVAL_YEARS, $years)));
        } catch (Exception) {
            return $dateTime->modify(sprintf('-%d years', $years));
        }
    }

    /**
     * 减月
     *
     * @param int $months
     * @param string|DateTime|null $dateTime
     * @return DateTime
     */
    public static function minusMonths(int $months, null|string|DateTime $dateTime = null): DateTime
    {
        $dateTime = self::setDateTime($dateTime);
        try {
            return $dateTime->sub(new DateInterval(sprintf(self::DATE_INTERVAL_MONTHS, $months)));
        } catch (Exception) {
            return $dateTime->modify(sprintf('-%d months', $months));
        }
    }

    /**
     * 减天
     *
     * @param int $days
     * @param string|DateTime|null $dateTime
     * @return DateTime
     */
    public static function minusDays(int $days, null|string|DateTime $dateTime = null): DateTime
    {
        $dateTime = self::setDateTime($dateTime);
        try {
            return $dateTime->sub(new DateInterval(sprintf(self::DATE_INTERVAL_DAYS, $days)));
        } catch (Exception) {
            return $dateTime->modify(sprintf('-%d days', $days));
        }
    }

    /**
     * 减小时
     *
     * @param int $hours
     * @param string|DateTime|null $dateTime
     * @return DateTime
     */
    public static function minusHours(int $hours, null|string|DateTime $dateTime = null): DateTime
    {
        $dateTime = self::setDateTime($dateTime);
        try {
            return $dateTime->sub(new DateInterval(sprintf(self::DATE_INTERVAL_HOURS, $hours)));
        } catch (Exception) {
            return $dateTime->modify(sprintf('-%d hours', $hours));
        }
    }

    /**
     * 减分
     *
     * @param int $minutes
     * @param string|DateTime|null $dateTime
     * @return DateTime
     */
    public static function minusMinutes(int $minutes, null|string|DateTime $dateTime = null): DateTime
    {
        $dateTime = self::setDateTime($dateTime);
        try {
            return $dateTime->sub(new DateInterval(sprintf(self::DATE_INTERVAL_MINUTES, $minutes)));
        } catch (Exception) {
            return $dateTime->modify(sprintf('-%d minutes', $minutes));
        }
    }

    /**
     * 减秒
     *
     * @param int $seconds
     * @param string|DateTime|null $dateTime
     * @return DateTime
     */
    public static function minusSeconds(int $seconds, null|string|DateTime $dateTime = null): DateTime
    {
        $dateTime = self::setDateTime($dateTime);
        try {
            return $dateTime->sub(new DateInterval(sprintf(self::DATE_INTERVAL_SECONDS, $seconds)));
        } catch (Exception) {
            return $dateTime->modify(sprintf('-%d seconds', $seconds));
        }
    }

    /**
     * 减周
     *
     * @param int $weeks
     * @param string|DateTime|null $dateTime
     * @return DateTime
     */
    public static function minusWeeks(int $weeks, null|string|DateTime $dateTime = null): DateTime
    {
        $dateTime = self::setDateTime($dateTime);
        try {
            return $dateTime->sub(new DateInterval(sprintf(self::DATE_INTERVAL_WEEKS, $weeks)));
        } catch (Exception) {
            return $dateTime->modify(sprintf('-%d weeks', $weeks));
        }
    }

    /**
     * 判断时间是否在之后
     *
     * @param string|DateTime|null $leftDateTime
     * @param string|DateTime|null $rightDateTime
     * @param string $format
     * @return bool
     */
    public static function isAfter(null|string|DateTime $leftDateTime, null|string|DateTime $rightDateTime = null, string $format = self::DATE_FORMAT): bool
    {
        return self::compareTo($leftDateTime, $rightDateTime, $format) > CommonConstant::ZERO;
    }

    /**
     * 比较大小，0表示时间相等 1左边比右边大 -1左边比右边小
     *
     * @param string|DateTime|null $leftDateTime
     * @param string|DateTime|null $rightDateTime
     * @param string $format
     * @return int
     */
    public static function compareTo(null|string|DateTime $leftDateTime, null|string|DateTime $rightDateTime = null, string $format = self::DATE_FORMAT): int
    {
        $leftDateTime = self::setDateTime($leftDateTime, $format);
        $rightDateTime = self::setDateTime($rightDateTime, $format);

        $leftTimestamp = $leftDateTime->getTimestamp();
        $rightTimestamp = $rightDateTime->getTimestamp();

        return $leftTimestamp === $rightTimestamp ? CommonConstant::ZERO : ($leftTimestamp > $rightTimestamp ? CommonConstant::ONE : CommonConstant::NEGATIVE);
    }

    /**
     * 判断时间是否在之前
     *
     * @param string|DateTime|null $leftDateTime
     * @param string|DateTime|null $rightDateTime
     * @param string $format
     * @return bool
     */
    public static function isBefore(null|string|DateTime $leftDateTime, null|string|DateTime $rightDateTime = null, string $format = self::DATE_FORMAT): bool
    {
        return self::compareTo($leftDateTime, $rightDateTime, $format) < CommonConstant::ZERO;
    }

    /**
     * 判断时间是否相等
     *
     * @param string|DateTime|null $leftDateTime
     * @param string|DateTime|null $rightDateTime
     * @param string $format
     * @return bool
     */
    public static function isEqual(null|string|DateTime $leftDateTime, null|string|DateTime $rightDateTime = null, string $format = self::DATE_FORMAT): bool
    {
        return self::compareTo($leftDateTime, $rightDateTime, $format) === CommonConstant::ZERO;
    }

    /**
     * 判断时间是否在范围内
     *
     * @param string|DateTime|null $leftDateTime
     * @param string|DateTime|null $rightDateTime
     * @param string|DateTime|null $dateTime
     * @param string $format
     * @return bool
     */
    public static function isBetween(null|string|DateTime $leftDateTime, null|string|DateTime $rightDateTime, null|string|DateTime $dateTime, string $format = self::DATE_FORMAT): bool
    {
        $leftDateTime = self::setDateTime($leftDateTime, $format);
        $rightDateTime = self::setDateTime($rightDateTime, $format);
        $dateTime = self::setDateTime($dateTime, $format);

        return self::compareTo($dateTime, $leftDateTime, $format) > CommonConstant::NEGATIVE && self::compareTo($dateTime, $rightDateTime, $format) < CommonConstant::ONE;
    }

    /**
     * 获取时间戳，单位秒
     *
     * @param string|DateTime|null $dateTime
     * @return int
     */
    public static function timestampForSecond(null|string|DateTime $dateTime = null): int
    {
        return self::setDateTime($dateTime)->getTimestamp();
    }

    /**
     * 获取时间戳，单位毫秒
     *
     * @param string|DateTime|null $dateTime
     * @return int
     */
    public static function timestampForMillisecond(null|string|DateTime $dateTime = null): int
    {
        $dateTime = self::setDateTime($dateTime);
        $seconds = $dateTime->getTimestamp();
        $milliseconds = (int)($dateTime->format('u') / 1000);
        return $seconds * 1000 + $milliseconds;
    }
}