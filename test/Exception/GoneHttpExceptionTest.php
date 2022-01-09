<?php

declare(strict_types = 1);

namespace ZfeggTest\PsrMvc\Exception;

use Zfegg\PsrMvc\Exception\GoneHttpException;
use Zfegg\PsrMvc\Exception\HttpException;

class GoneHttpExceptionTest extends HttpExceptionTest
{
    protected function createException(
        string $message = '',
        ?\Throwable $previous = null,
        int $code = 0,
        array $headers = []
    ): HttpException {
        return new GoneHttpException($message, $previous, $code, $headers);
    }
}
