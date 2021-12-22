<?php

namespace Zfegg\PsrMvc\Middleware;

use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class JsonSerializer implements MiddlewareInterface
{

    public function process(ServerRequestInterface $request, callable $next): ResponseInterface
    {
        return new JsonResponse($next());
    }
}