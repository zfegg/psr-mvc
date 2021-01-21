<?php declare(strict_types = 1);

namespace Zfegg\CallableHandlerDecorator;

use Closure;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use ReflectionFunction;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionParameter;
use Zfegg\CallableHandlerDecorator\Exception\InvalidArgumentException;

class ReflectionFactory
{
    public const DEFAULT_SEPARATOR = '@';

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var callable
     */
    private $paramNameConverter;

    /**
     * @var string
     */
    private $separator;

    public function __construct(
        ContainerInterface $container,
        callable $paramNameConverter = null,
        string $separator = self::DEFAULT_SEPARATOR
    ) {
        $this->container = $container;
        $this->paramNameConverter = $paramNameConverter ?:
            function ($name) {
                return $name;
            };
        $this->separator = $separator;
    }

    /**
     * Get call reflector.
     *
     * @return ReflectionFunction|ReflectionMethod
     * @throws \ReflectionException
     */
    private function getCallReflector(callable $callback)
    {
        if (is_object($callback) && ! $callback instanceof Closure) {
            $callback = [$callback, '__invoke'];
        }

        return is_array($callback)
            ? new ReflectionMethod($callback[0], $callback[1])
            : new ReflectionFunction($callback);
    }

    /**
     * Create param resolver.
     *
     * @param mixed $value
     */
    private function createResolver(int $resolverType, ReflectionParameter $parameter): callable
    {
        $name = ($this->paramNameConverter)($parameter->getName());
        switch ($resolverType) {
            case 1:
                return function (ServerRequestInterface $request) use ($name) {
                    return $request->getAttribute($name);
                };
            case 2:
                $value = $parameter->getDefaultValue();
                return function (ServerRequestInterface $request) use ($name, $value) {
                    return $request->getAttribute($name, $value);
                };
            case 3:
                return function (ServerRequestInterface $request): ServerRequestInterface {
                    return $request;
                };
            case 4:
                $type = $parameter->getType()->getName();
                return function (ServerRequestInterface $request) use ($name, $type) {
                    $value = $request->getAttribute($type, $request->getAttribute($name));
                    if ($value === null) {
                        return $this->container->get($type);
                    }

                    return $value;
                };
            case 5:
            default:
                $type = $parameter->getType()->getName();
                return function (ServerRequestInterface $request) use ($name, $type) {
                    return $request->getAttribute($type, $request->getAttribute($name));
                };
        }
    }

    private function parameterResolver(ReflectionParameter $parameter): callable
    {
        $type = $parameter->getType();
        $type = $type instanceof ReflectionNamedType ? $type->getName() : null;

        if ($type === 'array' ||
            $type === null ||
            (is_string($type) && ! class_exists($type) && ! interface_exists($type))
        ) {
            if ($parameter->isDefaultValueAvailable()) {
                return $this->createResolver(2, $parameter);
            }

            return $this->createResolver(1, $parameter);
        }

        if ($type === ServerRequestInterface::class) {
            return $this->createResolver(3, $parameter);
        }

        if ($this->container->has($type)) {
            return $this->createResolver(4, $parameter);
        }

        return $this->createResolver(5, $parameter);
    }

    /**
     * Normalize callback.
     *
     * @param callable|string $callback
     */
    private function normalize($callback): callable
    {
        if (is_string($callback) && strpos($callback, $this->separator) !== false) {
            [$class, $method] = explode($this->separator, $callback);

            return [$this->container->get($class), $method];
        }

        return $callback;
    }

    /**
     * Create CallableHandlerDecorator by callable or action.
     *
     * @param callable|string $callback
     */
    public function create($callback): CallableHandlerDecorator
    {
        $callback = $this->normalize($callback);
        $reflector = $this->getCallReflector($callback);

        $paramResolvers = [];
        foreach ($reflector->getParameters() as $parameter) {
            $paramResolvers[$parameter->getName()] = $this->parameterResolver($parameter);
        }

        return new CallableHandlerDecorator($callback, $paramResolvers);
    }

    public function exists(string $action): bool
    {
        [$class, $method] = explode($this->separator, $action) + ['', ''];

        return method_exists($class, $method) && $this->container->has($class);
    }
}
