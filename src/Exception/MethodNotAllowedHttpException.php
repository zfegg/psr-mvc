<?php

declare(strict_types = 1);

namespace Zfegg\PsrMvc\Exception;

class MethodNotAllowedHttpException extends HttpException
{
    public function __construct(
        array $allow,
        ?string $message = '',
        ?\Throwable $previous = null,
        ?int $code = 0,
        array $headers = []
    ) {
        $headers['Allow'] = strtoupper(implode(', ', $allow));

        parent::__construct(405, $message, $previous, $headers, $code);
    }
}
