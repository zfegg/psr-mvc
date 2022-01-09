<?php

declare(strict_types = 1);

namespace Zfegg\PsrMvc\Exception;

class NotFoundHttpException extends HttpException
{
    /**
     * @param string|null     $message  The internal exception message
     * @param \Throwable|null $previous The previous exception
     * @param int|null        $code     The internal exception code
     */
    public function __construct(
        ?string $message = '',
        ?\Throwable $previous = null,
        ?int $code = null,
        array $headers = []
    ) {
        parent::__construct(404, $message, $previous, $headers, $code);
    }
}
