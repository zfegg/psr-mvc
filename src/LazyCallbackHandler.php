<?php

declare(strict_types = 1);

namespace Zfegg\PsrMvc;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class LazyCallbackHandler implements RequestHandlerInterface
{
    private ?RequestHandlerInterface $handler = null;

    /**
     * @var callable|string
     */
    private $callback;

    public function __construct(
        private CallbackHandlerFactory $factory,
        callable|string $callback,
    ) {
        $this->callback = $callback;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        if (! $this->handler) {
            $this->handler = $this->factory->create($this->callback);
        }

        return $this->handler->handle($request);
    }
}
