<?php

declare(strict_types = 1);

namespace ZfeggTest\PsrMvc\Exception;

use Zfegg\PsrMvc\Exception\ConflictHttpException;
use Zfegg\PsrMvc\Exception\HttpException;

class ConflictHttpExceptionTest extends HttpExceptionTest
{
    protected function createException(
        string $message = '',
        ?\Throwable $previous = null,
        int $code = 0,
        array $headers = []
    ): HttpException {
        return new ConflictHttpException($message, $previous, $code, $headers);
    }
}
