<?php

declare(strict_types = 1);

namespace Zfegg\PsrMvc\Exception;

class LengthRequiredHttpException extends HttpException
{
    public function __construct(
        ?string $message = '',
        ?\Throwable $previous = null,
        ?int $code = null,
        array $headers = []
    ) {
        parent::__construct(411, $message, $previous, $headers, $code);
    }
}
