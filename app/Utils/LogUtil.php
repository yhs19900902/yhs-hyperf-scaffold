<?php

declare(strict_types=1);


namespace App\Utils;

use Hyperf\Context\ApplicationContext;
use Hyperf\Logger\LoggerFactory;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Log\LoggerInterface;

class LogUtil
{
    private function __construct()
    {

    }

    public static function get(string $name = 'app'): ?LoggerInterface
    {
        try {
            return ApplicationContext::getContainer()->get(LoggerFactory::class)->get($name);
        } catch (NotFoundExceptionInterface|ContainerExceptionInterface $e) {
            return null;
        }
    }
}