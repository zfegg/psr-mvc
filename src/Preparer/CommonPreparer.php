<?php

declare(strict_types = 1);

namespace Zfegg\PsrMvc\Preparer;

use Laminas\Diactoros\Response\EmptyResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class CommonPreparer implements ResultPreparableInterface
{
    public function prepare(ServerRequestInterface $request, mixed $result, array $options = []): ResponseInterface
    {
        if ($result === null) {
            return new EmptyResponse(204, $options['headers'] ?? []);
        }

        return $result;
    }

    public function supportsPreparation(ServerRequestInterface $request, mixed $result, array $options = []): bool
    {
        return $result instanceof ResponseInterface || $result === null;
    }
}
