<?php

declare(strict_types = 1);

namespace Zfegg\PsrMvc\PrepareResponse;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

interface PrepareResponseInterface
{
    public function prepare(ServerRequestInterface $request, mixed $result, array $options = []): ResponseInterface;
}
