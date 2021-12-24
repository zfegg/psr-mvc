<?php

declare(strict_types = 1);

namespace Zfegg\PsrMvc;

use Closure;
use Psr\Container\ContainerInterface;
use ReflectionFunction;
use ReflectionFunctionAbstract;
use ReflectionMethod;
use Zfegg\PsrMvc\Attribute\Middleware;
use Zfegg\PsrMvc\ParamResolver\ParamResolverManager;

class CallbackHandlerFactory
{
    public const DEFAULT_SEPARATOR = '@';

    private ContainerInterface $container;

    private string $separator;
    private array $defaultMiddlewares;
    private ParamResolverManager $manager;

    public function __construct(
        ContainerInterface $container,
        ParamResolverManager $paramResolverManager,
        array $defaultMiddlewares = [],
        string $separator = self::DEFAULT_SEPARATOR,
    ) {
        $this->container = $container;
        $this->separator = $separator;
        $this->defaultMiddlewares = $defaultMiddlewares;
        $this->manager = $paramResolverManager;
    }

    /**
     * Get call reflector.
     *
     * @throws \ReflectionException
     */
    private function getCallReflector(callable $callback): ReflectionFunction|ReflectionMethod
    {
        if (is_object($callback) && ! $callback instanceof Closure) {
            $callback = [$callback, '__invoke'];
        }

        return is_array($callback)
            ? new ReflectionMethod($callback[0], $callback[1])
            : new ReflectionFunction($callback);
    }

    /**
     * Normalize callback.
     */
    private function normalize(callable|string $callback): callable
    {
        if (is_string($callback) && str_contains($callback, $this->separator)) {
            [$class, $method] = explode($this->separator, $callback);

            return [$this->container->get($class), $method];
        }

        return $callback;
    }

    private function initMiddleware(ReflectionFunctionAbstract $ref): array
    {
        $middlewares = [];
        foreach ($ref->getAttributes(Middleware::class) as $refAttr) {
            $attr = $refAttr->newInstance();

            $middlewares[] = $this->container->get($attr->name, $attr->options);
        }

        return $middlewares ?: $this->defaultMiddlewares;
    }

    /**
     * Create CallableHandlerDecorator by callable or action.
     */
    public function create(callable|string $callback): CallbackHandler
    {
        $callback = $this->normalize($callback);
        $reflector = $this->getCallReflector($callback);

        $paramResolvers = [];
        foreach ($reflector->getParameters() as $parameter) {
            $paramResolvers[$parameter->getName()] = $this->manager->resolver($parameter);
        }

        return new CallbackHandler($callback, $paramResolvers, $this->initMiddleware($reflector));
    }

    public function exists(string $action): bool
    {
        [$class, $method] = explode($this->separator, $action) + ['', ''];

        return method_exists($class, $method) && $this->container->has($class);
    }
}
