<?php

namespace Zfegg\PsrMvc\Preparer;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use SplStack;
use Zfegg\PsrMvc\Exception\InvalidArgumentException;

class PreparerStack extends SplStack implements ResultPreparableInterface
{
    public function prepare(ServerRequestInterface $request, mixed $result, array $options = []): ResponseInterface
    {
        /** @var ResultPreparableInterface $preparer */
        foreach ($this as $preparer) {
            if ($preparer->supportsPreparation($request, $result, $options)) {
                return $preparer->prepare($request, $result, $options);
            }
        }

        throw new InvalidArgumentException("Resolve result error.");
    }

    public function supportsPreparation(ServerRequestInterface $request, mixed $result, array $options = []): bool
    {
        return $this->count() > 0;
    }
}