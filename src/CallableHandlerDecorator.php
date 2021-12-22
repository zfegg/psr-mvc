<?php declare(strict_types = 1);

namespace Zfegg\CallableHandlerDecorator;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zfegg\CallableHandlerDecorator\Middleware\MiddlewareInterface;

class CallableHandlerDecorator implements RequestHandlerInterface
{
    /**
     * @var callable
     */
    private $callback;


    public function __construct(
        callable $callback,

        /** @var callable[] */
        private array $paramResolvers,

        /** @var MiddlewareInterface[] */
        private array $middlewares = []
    ) {
        $this->callback = $callback;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return array_reduce(
            $this->middlewares,
            fn(callable $next, MiddlewareInterface $middleware) => fn() => $middleware->process($request, $next),
            function () use ($request) {
                $params = [];
                foreach ($this->paramResolvers as $resolver) {
                    $params[] = $resolver($request);
                }

                return call_user_func_array($this->callback, $params);
            }
        )();
    }
}
