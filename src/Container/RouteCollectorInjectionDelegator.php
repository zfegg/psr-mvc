<?php


namespace Zfegg\PsrMvc\Container;


use Doctrine\Common\Annotations\PsrCachedReader;
use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use Laminas\ServiceManager\Exception\ServiceNotCreatedException;
use Laminas\ServiceManager\Exception\ServiceNotFoundException;
use Laminas\ServiceManager\Factory\DelegatorFactoryInterface;
use Mezzio\MiddlewareFactory;
use Mezzio\Router\RouteCollector;
use Zfegg\PsrMvc\Attribute\Route;
use Zfegg\PsrMvc\CallbackHandlerFactory;
use Zfegg\PsrMvc\Route\RouteMetadata;

class RouteCollectorInjectionDelegator implements DelegatorFactoryInterface
{

    public function __invoke(ContainerInterface $container, $name, callable $callback, ?array $options = null)
    {
        /** @var RouteCollector $router */
        $router = $callback();
        $metadata = $container->get(RouteMetadata::class);
        $middlewareFactory = $container->get(MiddlewareFactory::class);
        $handlerFactory = $container->get(CallbackHandlerFactory::class);

        $routes = $metadata->getRoutes();

        /**
         * @var Route $routeMeta
         */
        foreach ($routes as [$routeMeta, [$className, $action]]) {
            $route = $router->route(
                $routeMeta->path,
                $middlewareFactory->prepare(array_merge(
                    $routeMeta->middlewares,
                    [$handlerFactory->create([$container->get($className), $action])],
                )),
                $routeMeta->methods,
                $routeMeta->name,
            );
            $route->setOptions($routeMeta->options);
        }

        return $router;
    }
}