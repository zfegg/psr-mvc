<?php

declare(strict_types = 1);

namespace ZfeggTest\PsrMvc\Exception;

use Zfegg\PsrMvc\Exception\HttpException;
use Zfegg\PsrMvc\Exception\PreconditionFailedHttpException;

class PreconditionFailedHttpExceptionTest extends HttpExceptionTest
{
    protected function createException(
        string $message = '',
        ?\Throwable $previous = null,
        int $code = 0,
        array $headers = []
    ): HttpException {
        return new PreconditionFailedHttpException($message, $previous, $code, $headers);
    }
}
