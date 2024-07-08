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

use App\Processor\AppendRequestIdProcessor;
use function Hyperf\Support\env;

return [
    'default' => [
        'handler' => [
            'class' => Monolog\Handler\RotatingFileHandler::class,
            'constructor' => [
//                'filename' => BASE_PATH . '/runtime/logs/' . env('APP_NAME', 'skeleton') . '.log',
                'filename' => '/data/runtime/logs/' . env('APP_NAME', 'skeleton') . '.log',
                'level' => Monolog\Logger::DEBUG,
            ],
        ],
        'formatter' => [
            'class' => Monolog\Formatter\LineFormatter::class,
            'constructor' => [
                'format' => "%datetime% %level_name%\n\t\t[" . env('APP_NAME', 'skeleton') . ",%extra%]\n\t\t- %channel%\n\t\t%message% %context%\n",
                'dateFormat' => 'Y-m-d H:i:s.u',
                'allowInlineLineBreaks' => true,
            ],
        ],
        'processors' => [
            [
                'class' => AppendRequestIdProcessor::class,
            ],
        ]
    ],
];
