<?php

declare(strict_types=1);


namespace App\Exception\Handler;

use App\POJO\BusinessResponse;
use Hyperf\ExceptionHandler\Annotation\ExceptionHandler as AnnotationExceptionHandler;
use Hyperf\ExceptionHandler\ExceptionHandler;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Hyperf\Validation\ValidationException;
use Swow\Psr7\Message\ResponsePlusInterface;
use Throwable;

#[AnnotationExceptionHandler(priority: 100)]
class ValidationExceptionHandler extends ExceptionHandler
{
    public function handle(Throwable $throwable, ResponsePlusInterface $response)
    {
        $this->stopPropagation();
        /** @var ValidationException $throwable */
        $body = $throwable->validator->errors()->first();
        if (!$response->hasHeader('content-type')) {
            $response = $response->addHeader('content-type', 'application/json; charset=utf-8');
        }
        $businessResponse = new BusinessResponse();
        return $response->setStatus($throwable->status)->setBody(new SwooleStream(json_encode($businessResponse->fail($throwable->status, $body), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK | JSON_PARTIAL_OUTPUT_ON_ERROR | JSON_INVALID_UTF8_IGNORE)));
    }

    public function isValid(Throwable $throwable): bool
    {
        return $throwable instanceof ValidationException;
    }
}