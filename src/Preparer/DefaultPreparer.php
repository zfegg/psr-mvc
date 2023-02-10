<?php

declare(strict_types = 1);

namespace Zfegg\PsrMvc\Preparer;

use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class DefaultPreparer implements ResultPreparableInterface
{

    public function supportsPreparation(ServerRequestInterface $request, mixed $result, array $options = []): bool
    {
        return true;
    }

    public function prepare(ServerRequestInterface $request, mixed $result, array $options = []): ResponseInterface
    {
        $status = $options['status'] ?? 200;
        $headers = $options['headers'] ?? [];

        if (is_string($result)) {
            return new HtmlResponse($result, $status, $headers);
        } else {
            return new JsonResponse($result, $status, $headers);
        }
    }
}
