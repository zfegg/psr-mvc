<?php declare(strict_types = 1);

namespace Zfegg\CallableHandlerDecorator;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class CallableHandlerDecorator implements RequestHandlerInterface
{
    /**
     * @var callable
     */
    private $callback;

    /** @var callable[] */
    private $paramResolvers;

    public function __construct(callable $callback, array $paramResolvers)
    {
        $this->callback = $callback;
        $this->paramResolvers = $paramResolvers;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $params = [];
        foreach ($this->paramResolvers as $resolver) {
            $params[] = $resolver($request);
        }

        return call_user_func_array($this->callback, $params);
    }
}
