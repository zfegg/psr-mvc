<?php

declare(strict_types = 1);

namespace Zfegg\PsrMvc;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zfegg\PsrMvc\PrepareResponse\PrepareResponseInterface;

class CallbackHandler implements RequestHandlerInterface
{
    /**
     * @var callable
     */
    private $callback;

    public function __construct(
        callable $callback,
        /** @var callable[] */
        private array $paramResolvers,
        private PrepareResponseInterface $prepareResponse,
        private array $options = []
    ) {
        $this->callback = $callback;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $params = [];
        foreach ($this->paramResolvers as $resolver) {
            $params[] = $resolver($request);
        }

        $result = call_user_func_array($this->callback, $params);

        return $this->prepareResponse->prepare($request, $result, $this->options);
    }
}
