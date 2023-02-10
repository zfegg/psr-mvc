<?php

declare(strict_types=1);

namespace Laminas\HttpHandlerRunner\Emitter;

function headers_sent(?string &$filename = null, ?int &$line = null): bool
{
    return false;
}

function header(string $headerName, bool $replace = true, ?int $httpResponseCode = null): void
{
}