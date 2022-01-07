<?php

declare(strict_types = 1);

namespace Zfegg\PsrMvc;

use Closure;
use Psr\Container\ContainerInterface;
use ReflectionFunction;
use ReflectionFunctionAbstract;
use ReflectionMethod;
use Zfegg\PsrMvc\Attribute\PrepareResponse;
use Zfegg\PsrMvc\ParamResolver\ParamResolverManager;
use Zfegg\PsrMvc\PrepareResponse\PrepareResponseInterface;

class CallbackHandlerFactory
{
    public const DEFAULT_SEPARATOR = '@';

    public function __construct(
        private ContainerInterface $container,
        private ParamResolverManager $paramResolverManager,
        private PrepareResponseInterface $defaultPrepareResponse,
        private string $separator = self::DEFAULT_SEPARATOR,
    ) {
    }

    public function getSeparator(): string
    {
        return $this->separator;
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

    /**
     * Create CallableHandlerDecorator by callable or action.
     */
    public function create(callable|string $callback): CallbackHandler
    {
        $callback = $this->normalize($callback);
        $reflector = $this->getCallReflector($callback);

        $paramResolvers = [];
        foreach ($reflector->getParameters() as $parameter) {
            $paramResolvers[$parameter->getName()] = $this->paramResolverManager->resolver($parameter);
        }

        // Set prepare response.
        $prepareResponse = $this->defaultPrepareResponse;
        $options = [];
        foreach ($reflector->getAttributes(PrepareResponse::class) as $refAttr) {
            /** @var PrepareResponse $attr */
            $attr = $refAttr->newInstance();
            $prepareResponse = $this->container->get($attr->name);
            $options = $attr->options;
        }

        return new CallbackHandler(
            $callback,
            $paramResolvers,
            $prepareResponse,
            $options
        );
    }

    public function exists(string $callback): bool
    {
        [$class, $method] = explode($this->separator, $callback) + ['', ''];

        return method_exists($class, $method) && $this->container->has($class);
    }
}
