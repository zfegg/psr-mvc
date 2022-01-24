<?php

declare(strict_types = 1);

namespace Zfegg\PsrMvc\PrepareResponse;

use Laminas\Diactoros\Response\EmptyResponse;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class DefaultResponse implements PrepareResponseInterface
{
    public function prepare(ServerRequestInterface $request, mixed $result, array $options = []): ResponseInterface
    {
        if ($result instanceof ResponseInterface) {
            return $result;
        }

        if ($result === null) {
            return new EmptyResponse(204, $options['headers'] ?? []);
        }

        if (is_string($result)) {
            return new HtmlResponse($result, $options['status'] ?? 200, $options['headers'] ?? []);
        } else {
            return new JsonResponse($result, $options['status'] ?? 200, $options['headers'] ?? []);
        }
    }
}
