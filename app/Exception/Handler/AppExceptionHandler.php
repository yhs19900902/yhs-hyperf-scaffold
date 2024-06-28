<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace App\Exception\Handler;

use App\Constants\CommonConstant;
use App\POJO\BusinessResponse;
use App\Utils\LogUtil;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\ExceptionHandler\ExceptionHandler;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Throwable;

class AppExceptionHandler extends ExceptionHandler
{
    public LoggerInterface $appLogger;

    public function __construct(protected StdoutLoggerInterface $logger)
    {
        $this->appLogger = LogUtil::get(AppExceptionHandler::class);
    }

    public function handle(Throwable $throwable, ResponseInterface $response): ResponseInterface
    {
        $errorMsg = sprintf('%s[%s] ERROR:%s', $throwable->getLine(), $throwable->getFile(), $throwable->getMessage());
        $this->logger->error($errorMsg);
        $this->logger->error($throwable->getTraceAsString());
        $this->logger->error($errorMsg);

        $businessResponse = new BusinessResponse();
        return $response->withHeader('Content-type', CommonConstant::CONTENT_TYPE)->withStatus(500)->withBody(new SwooleStream(json_encode($businessResponse->fail(500, 'Internal Server Error.'), JSON_UNESCAPED_UNICODE)));
    }

    public function isValid(Throwable $throwable): bool
    {
        return true;
    }
}
