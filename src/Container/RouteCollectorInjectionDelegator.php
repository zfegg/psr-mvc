<?php

declare(strict_types = 1);

namespace Zfegg\PsrMvc\Container;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\DelegatorFactoryInterface;
use Mezzio\MiddlewareFactory;
use Mezzio\Router\RouteCollectorInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zfegg\PsrMvc\CallbackHandlerFactory;
use Zfegg\PsrMvc\Route\RouteMetadata;

class RouteCollectorInjectionDelegator implements DelegatorFactoryInterface
{

    /** @inheritdoc */
    public function __invoke(
        ContainerInterface $container,
        $name,
        callable $callback,
        ?array $options = null
    ): RouteCollectorInterface {
        /** @var RouteCollectorInterface $router */
        $router = $callback();
        $metadata = $container->get(RouteMetadata::class);
        $middlewareFactory = $container->get(MiddlewareFactory::class);
        $handlerFactory = $container->get(CallbackHandlerFactory::class);

        $routes = $metadata->getRoutes();

        /**
         * @var \Zfegg\PsrMvc\Attribute\Route $routeMeta
         */
        foreach ($routes as [$routeMeta, [$className, $action]]) {
            $route = $router->route(
                $routeMeta->path,
                $middlewareFactory->prepare([
                    ...$routeMeta->middlewares,
                    // Lazy load
                    static fn(ServerRequestInterface $request) => $handlerFactory
                        ->create([$container->get($className), $action])
                        ->handle($request)
                ]),
                $routeMeta->methods,
                $routeMeta->name,
            );
            $route->setOptions($routeMeta->options);
        }

        return $router;
    }
}
