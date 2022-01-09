<?php

declare(strict_types = 1);

namespace ZfeggTest\PsrMvc\Exception;

use Zfegg\PsrMvc\Exception\AccessDeniedHttpException;
use Zfegg\PsrMvc\Exception\HttpException;

class AccessDeniedHttpExceptionTest extends HttpExceptionTest
{
    protected function createException(
        string $message = '',
        ?\Throwable $previous = null,
        int $code = 0,
        array $headers = []
    ): HttpException {
        return new AccessDeniedHttpException($message, $previous, $code, $headers);
    }
}
