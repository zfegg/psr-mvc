<?php

declare(strict_types = 1);

namespace Zfegg\PsrMvc\Preparer;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

interface ResultPreparableInterface
{
    public function prepare(ServerRequestInterface $request, mixed $result, array $options = []): ResponseInterface;
}
