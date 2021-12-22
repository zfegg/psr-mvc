<?php

declare(strict_types = 1);

namespace Zfegg\PsrMvc\Middleware;

use Psr\Http\Message\ServerRequestInterface;

interface MiddlewareInterface
{

    public function process(ServerRequestInterface $request, callable $next): mixed;
}
