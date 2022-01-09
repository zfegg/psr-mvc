<?php

declare(strict_types = 1);

namespace Zfegg\PsrMvc\Exception;

class UnauthorizedHttpException extends HttpException
{
    /**
     * @param string          $challenge WWW-Authenticate challenge string
     * @param string|null     $message   The internal exception message
     * @param \Throwable|null $previous  The previous exception
     * @param int|null        $code      The internal exception code
     */
    public function __construct(
        string $challenge,
        ?string $message = '',
        ?\Throwable $previous = null,
        ?int $code = null,
        array $headers = []
    ) {
        $headers['WWW-Authenticate'] = $challenge;

        parent::__construct(401, $message, $previous, $headers, $code);
    }
}
