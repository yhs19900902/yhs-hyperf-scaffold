<?php

declare(strict_types=1);


namespace App\Processor;

use Hyperf\Context\Context;
use Hyperf\Coroutine\Coroutine;
use Monolog\LogRecord;
use Monolog\Processor\ProcessorInterface;

class AppendRequestIdProcessor implements ProcessorInterface
{
    public const REQUEST_ID = 'log.request.id';

    public function __invoke(array|LogRecord $record): array|LogRecord
    {
        $record['extra']['requestId'] = Context::getOrSet(self::REQUEST_ID, uniqid());
        $coroutineId = Coroutine::id();
        $record['extra']['parentId'] = Coroutine::parentId($coroutineId);
        $record['extra']['coroutineId'] = $coroutineId;
        return $record;
    }
}