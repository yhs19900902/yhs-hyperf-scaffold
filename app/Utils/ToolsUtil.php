<?php

declare(strict_types=1);


namespace App\Utils;

use App\Constants\CommonConstant;
use Hyperf\Context\ApplicationContext;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\Stringable\Str;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

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

    /**
     * 获取ip
     *
     * @return string
     */
    public static function getRealIp(): string
    {
        try {
            $request = ApplicationContext::getContainer()->get(RequestInterface::class);
        } catch (NotFoundExceptionInterface|ContainerExceptionInterface $e) {
            $request = [];
        }
        $headers = $request->getHeaders();

        if (isset($headers['x-forwarded-for']) && !empty($headers['x-forwarded-for'][CommonConstant::ZERO])) {
            return $headers['x-forwarded-for'][CommonConstant::ZERO];
        } elseif (isset($headers['X-Forwarded-For']) && !empty($headers['X-Forwarded-For'][CommonConstant::ZERO])) {
            return $headers['X-Forwarded-For'][CommonConstant::ZERO];
        } elseif (isset($headers['x-real-ip']) && !empty($headers['x-real-ip'][CommonConstant::ZERO])) {
            return $headers['x-real-ip'][CommonConstant::ZERO];
        } elseif (isset($headers['X-Real-IP']) && !empty($headers['X-Real-IP'][CommonConstant::ZERO])) {
            return $headers['X-Real-IP'][CommonConstant::ZERO];
        }

        $serverParams = $request->getServerParams();
        return $serverParams['remote_addr'] ?? CommonConstant::EMPTY;

    }

    private function ____construct()
    {

    }
}