<?php declare(strict_types = 1);

namespace Zfegg\PsrMvc;

use Closure;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use ReflectionFunction;
use ReflectionFunctionAbstract;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionParameter;
use Zfegg\PsrMvc\Attribute\FromAttribute;
use Zfegg\PsrMvc\Attribute\FromBody;
use Zfegg\PsrMvc\Attribute\FromContainer;
use Zfegg\PsrMvc\Attribute\FromCookie;
use Zfegg\PsrMvc\Attribute\FromHeader;
use Zfegg\PsrMvc\Attribute\FromQuery;
use Zfegg\PsrMvc\Attribute\FromServer;
use Zfegg\PsrMvc\Attribute\InjectFrom;
use Zfegg\PsrMvc\Attribute\Middleware;

class CallbackHandlerFactory
{
    public const DEFAULT_SEPARATOR = '@';

    private const FROM_ATTR = 1;
    private const FROM_ATTR2 = 2;
    private const FROM_REQUEST = 3;
    private const FROM_CONTAINER = 4;
    private const FROM_QUERY = 5;
    private const FROM_BODY = 6;
    private const FROM_COOKIE = 7;
    private const FROM_HEADER = 8;
    private const FROM_SERVER = 9;

    private ContainerInterface $container;

    /**
     * @var callable
     */
    private $paramNameTransformer;

    private string $separator;
    private array $defaultMiddlewares;

    public function __construct(
        ContainerInterface $container,
        callable $paramNameTransformer = null,
        array $defaultMiddlewares = [],
        string $separator = self::DEFAULT_SEPARATOR,
    ) {
        $this->container = $container;
        $this->paramNameTransformer = $paramNameTransformer ?: fn($name) => $name;
        $this->separator = $separator;
        $this->defaultMiddlewares = $defaultMiddlewares;
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
     */
    private function getParamResolver(
        int $resolverType,
        ?string $name = null,
        ?string $type = null,
        mixed $default = null
    ): callable
    {
        switch ($resolverType) {
            case self::FROM_ATTR:
                return fn(ServerRequestInterface $request) => $request->getAttribute($name, $default);
            case self::FROM_REQUEST:
                return static fn(ServerRequestInterface $request): ServerRequestInterface => $request;
            case self::FROM_CONTAINER:
                return fn() => $this->container->get($name);
            case self::FROM_QUERY:
                return fn(ServerRequestInterface $request): mixed => $request->getQueryParams()[$name] ?? $default;
            case self::FROM_BODY:
                return fn(ServerRequestInterface $request): mixed => $request->getParsedBody()[$name] ?? $default;
            case self::FROM_COOKIE:
                return fn(ServerRequestInterface $request): mixed => $request->getCookieParams()[$name] ?? $default;
            case self::FROM_HEADER:
                return fn(ServerRequestInterface $request): string => $request->getHeaderLine($name);
            case self::FROM_SERVER:
                return fn(ServerRequestInterface $request): mixed => $request->getServerParams()[$name] ?? $default;
            case self::FROM_ATTR2:
            default:
                return function (ServerRequestInterface $request) use ($name, $type) {
                    return $request->getAttribute($type, $request->getAttribute($name));
                };
        }
    }

    private function createParamResolver(ReflectionParameter $parameter): callable
    {
        $type = $parameter->getType();
        $type = $type instanceof ReflectionNamedType ? $type->getName() : null;
        $name = ($this->paramNameTransformer)($parameter->getName());

        if ($resolver = $this->resolveByAttribute($parameter, $name)) {
            return $resolver;
        }

        if ($type === ServerRequestInterface::class) {
            return $this->getParamResolver(self::FROM_REQUEST);
        }

        if ($type === 'array' ||
            $type === null ||
            (is_string($type) && ! class_exists($type) && ! interface_exists($type))
        ) {
            $defaultValue = $parameter->isDefaultValueAvailable()
                ? $parameter->getDefaultValue()
                : null;

            return $this->getParamResolver(self::FROM_ATTR, $name, default: $defaultValue);
        }

        if ($this->container->has($type)) {
            return $this->getParamResolver(self::FROM_CONTAINER, $type);
        }

        return $this->getParamResolver(self::FROM_ATTR2, $name, $type);
    }


    private function resolveByAttribute(ReflectionParameter $parameter, string $name): ?callable
    {
        $attrs = $parameter->getAttributes(InjectFrom::class, 2);

        foreach ($attrs as $attrRef) {
            $attr = $attrRef->newInstance();
            $name = $attr->name ?? $name;
            $defaultValue = $parameter->isDefaultValueAvailable()
                ? $parameter->getDefaultValue()
                : null;

            switch ($attrRef->getName()) {
                case FromAttribute::class:
                    return $this->getParamResolver(self::FROM_ATTR, $name, default: $defaultValue);
                case FromQuery::class:
                    return $this->getParamResolver(self::FROM_QUERY, $name, default: $defaultValue);
                case FromBody::class:
                    return $this->getParamResolver(self::FROM_BODY, $name, default: $defaultValue);
                case FromContainer::class:
                    return $this->getParamResolver(self::FROM_CONTAINER, $name);
                case FromCookie::class:
                    return $this->getParamResolver(self::FROM_COOKIE, $name, default: $defaultValue);
                case FromHeader::class:
                    return $this->getParamResolver(self::FROM_HEADER, $name, default: $defaultValue);
                case FromServer::class:
                    return $this->getParamResolver(self::FROM_SERVER, $name, default: $defaultValue);
                default:
                    return null;
            }
        }

        return null;
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
            $paramResolvers[$parameter->getName()] = $this->createParamResolver($parameter);
        }

        return new CallbackHandler($callback, $paramResolvers, $this->initMiddleware($reflector));
    }

    public function exists(string $action): bool
    {
        [$class, $method] = explode($this->separator, $action) + ['', ''];

        return method_exists($class, $method) && $this->container->has($class);
    }

}
