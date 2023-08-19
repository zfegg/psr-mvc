<?php

declare(strict_types = 1);

namespace Zfegg\PsrMvc\Utils;

use Psr\Http\Message\ServerRequestInterface;

class RequestUtils
{
    public static function isXmlHttpRequest(ServerRequestInterface $request): bool
    {
        return $request->getHeaderLine('X-Requested-With') == 'XMLHttpRequest';
    }
}
