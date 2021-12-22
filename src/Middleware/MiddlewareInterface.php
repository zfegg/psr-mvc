<?php

namespace Zfegg\CallableHandlerDecorator\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

interface MiddlewareInterface
{

    public function process(ServerRequestInterface $request, callable $next): mixed;
}