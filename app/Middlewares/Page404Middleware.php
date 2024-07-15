<?php

declare(strict_types=1);


namespace App\Middlewares;

use App\Common\BusinessResult;
use App\Utils\LogUtil;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Hyperf\HttpServer\CoreMiddleware;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;

class Page404Middleware extends CoreMiddleware
{
    /**
     * @var LoggerInterface 日志
     */
    private LoggerInterface $logger;

    public function __construct(ContainerInterface $container, string $serverName)
    {
        parent::__construct($container, $serverName);
        $this->logger = LogUtil::get(Page404Middleware::class);
    }

    /**
     * 404页面
     *
     * @param ServerRequestInterface $request
     * @return \Psr\Http\Message\ResponseInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function handleNotFound(ServerRequestInterface $request): \Psr\Http\Message\ResponseInterface
    {
        $this->logger->error('Page not found. uri:' . $request->getUri()->getPath() . ' method: ' . $request->getMethod());
        return $this->container->get(ResponseInterface::class)->json(BusinessResult::fail(404, 'Page not found')->toArray())->withStatus(404);
    }

    /**
     * 405页面
     *
     * @param array $methods
     * @param ServerRequestInterface $request
     * @return \Psr\Http\Message\ResponseInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function handleMethodNotAllowed(array $methods, ServerRequestInterface $request): \Psr\Http\Message\ResponseInterface
    {
        $this->logger->error('Method not allowed. uri:' . $request->getUri()->getPath() . ' method:' . $request->getMethod());
        return $this->container->get(ResponseInterface::class)->json(BusinessResult::fail(405, 'Method not allowed')->toArray())->withStatus(405);
    }
}