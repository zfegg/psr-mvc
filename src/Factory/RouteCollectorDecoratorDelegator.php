<?php


namespace Zfegg\CallableHandlerDecorator\Factory;


use Doctrine\Common\Annotations\PsrCachedReader;
use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use Laminas\ServiceManager\Exception\ServiceNotCreatedException;
use Laminas\ServiceManager\Exception\ServiceNotFoundException;
use Laminas\ServiceManager\Factory\DelegatorFactoryInterface;
use Mezzio\MiddlewareFactory;
use Mezzio\Router\RouteCollector;
use Zfegg\CallableHandlerDecorator\Attribute\Route;
use Zfegg\CallableHandlerDecorator\ReflectionFactory;
use Zfegg\CallableHandlerDecorator\Router\RouteMetadata;

class RouteCollectorDecoratorDelegator implements DelegatorFactoryInterface
{

    public function __invoke(ContainerInterface $container, $name, callable $callback, ?array $options = null)
    {
        /** @var RouteCollector $router */
        $router = $callback();
        $metadata = $container->get(RouteMetadata::class);
        $middlewareFactory = $container->get(MiddlewareFactory::class);
        $handlerFactory = $container->get(ReflectionFactory::class);

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