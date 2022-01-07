<?php

declare(strict_types = 1);

namespace Zfegg\PsrMvc\Routing;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class Group
{

    private array $routes = [];
    private string $prefix;
    private string $name;
    private array $middlewares;

    /** @var Group[] */
    private array $children = [];

    public function __construct(string $prefix = '', array $middlewares = [], string $name = '')
    {
        $this->prefix = $prefix;
        $this->name = $name;
        $this->middlewares = $middlewares;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPrefix(): string
    {
        return $this->prefix;
    }

    public function getMiddlewares(): array
    {
        return $this->middlewares;
    }

    /**
     * Add a route for the route middleware to match.
     *
     * @param string|array|callable|MiddlewareInterface|RequestHandlerInterface $middleware
     *     Middleware or request handler (or service name resolving to one of
     *     those types) to associate with route.
     */
    public function route(
        string $path,
        string|array|callable|MiddlewareInterface|RequestHandlerInterface $middleware,
        ?array $methods = null,
        ?string $name = null,
        array $options = []
    ): self {
        $name = $name === null ? null : ($this->name . $name);
        $config = [
            'path' => $this->prefix . $path,
            'middleware' => [
                ...$this->middlewares,
                ...(is_array($middleware) ? $middleware : [$middleware]),
            ],
            'allowed_methods' => $methods,
            'name' => $name,
            'options' => $options,
        ];

        if ($name) {
            $this->routes[$name] = $config;
        } else {
            $this->routes[] = $config;
        }

        return $this;
    }

    /**
     * @param string|array|callable|MiddlewareInterface|RequestHandlerInterface $middleware
     *     Middleware or request handler (or service name resolving to one of
     *     those types) to associate with route.
     */
    public function get(
        string $path,
        string|array|callable|MiddlewareInterface|RequestHandlerInterface $middleware,
        ?string $name = null,
        array $options = []
    ): self {
        return $this->route($path, $middleware, ['GET'], $name, $options);
    }

    /**
     * @param string|array|callable|MiddlewareInterface|RequestHandlerInterface $middleware
     *     Middleware or request handler (or service name resolving to one of
     *     those types) to associate with route.
     */
    public function post(
        string $path,
        string|array|callable|MiddlewareInterface|RequestHandlerInterface $middleware,
        ?string $name = null,
        array $options = []
    ): self {
        return $this->route($path, $middleware, ['POST'], $name, $options);
    }

    /**
     * @param string|array|callable|MiddlewareInterface|RequestHandlerInterface $middleware
     *     Middleware or request handler (or service name resolving to one of
     *     those types) to associate with route.
     * @param null|string                                                       $name The name of the route.
     */
    public function put(
        string $path,
        string|array|callable|MiddlewareInterface|RequestHandlerInterface $middleware,
        ?string $name = null,
        array $options = []
    ): self {
        return $this->route($path, $middleware, ['PUT'], $name, $options);
    }

    /**
     * @param string|array|callable|MiddlewareInterface|RequestHandlerInterface $middleware
     *     Middleware or request handler (or service name resolving to one of
     *     those types) to associate with route.
     * @param null|string                                                       $name The name of the route.
     */
    public function patch(
        string $path,
        string|array|callable|MiddlewareInterface|RequestHandlerInterface $middleware,
        ?string $name = null,
        array $options = []
    ): self {
        return $this->route($path, $middleware, ['PATCH'], $name, $options);
    }

    /**
     * @param string|array|callable|MiddlewareInterface|RequestHandlerInterface $middleware
     *     Middleware or request handler (or service name resolving to one of
     *     those types) to associate with route.
     */
    public function delete(
        string $path,
        string|array|callable|MiddlewareInterface|RequestHandlerInterface $middleware,
        ?string $name = null,
        array $options = []
    ): self {
        return $this->route($path, $middleware, ['DELETE'], $name, $options);
    }

    /**
     * @param string|array|callable|MiddlewareInterface|RequestHandlerInterface $middleware
     *     Middleware or request handler (or service name resolving to one of
     *     those types) to associate with route.
     * @param null|string                                                       $name The name of the route.
     */
    public function any(
        string $path,
        string|array|callable|MiddlewareInterface|RequestHandlerInterface $middleware,
        ?string $name = null,
        array $options = []
    ): self {
        return $this->route($path, $middleware, null, $name, $options);
    }

    public function getRoutes(): array
    {
        $children = array_map(fn($child) => $child->getRoutes(), $this->children);

        return array_merge(
            $this->routes,
            ...$children
        );
    }

    public function group(string $prefix, array $middlewares = [], string $name = ''): self
    {
        $child = new Group($this->prefix . $prefix, array_merge($this->middlewares, $middlewares), $this->name . $name);

        $this->children[] = $child;

        return $child;
    }
}
