<?php

declare(strict_types = 1);

namespace Zfegg\PsrMvc\Exception;

class ConflictHttpException extends HttpException
{
    public function __construct(?string $message = '', ?\Throwable $previous = null, int $code = 0, array $headers = [])
    {
        parent::__construct(409, $message, $previous, $headers, $code);
    }
}
