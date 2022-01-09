<?php

declare(strict_types = 1);

namespace ZfeggTest\PsrMvc\Exception;

use Zfegg\PsrMvc\Exception\BadRequestHttpException;
use Zfegg\PsrMvc\Exception\HttpException;

class BadRequestHttpExceptionTest extends HttpExceptionTest
{
    protected function createException(
        string $message = '',
        ?\Throwable $previous = null,
        ?int $code = null,
        array $headers = []
    ): HttpException {
        return new BadRequestHttpException($message, $previous, $code, $headers);
    }
}
