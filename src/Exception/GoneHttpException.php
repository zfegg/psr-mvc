<?php

declare(strict_types = 1);

namespace Zfegg\PsrMvc\Exception;

class GoneHttpException extends HttpException
{
    public function __construct(?string $message = '', ?\Throwable $previous = null, int $code = 0, array $headers = [])
    {
        parent::__construct(410, $message, $previous, $headers, $code);
    }
}
