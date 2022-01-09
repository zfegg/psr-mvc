<?php

declare(strict_types = 1);

namespace Zfegg\PsrMvc\Exception;

class ServiceUnavailableHttpException extends HttpException
{
    /**
     * @param int|string|null $retryAfter The number of seconds or HTTP-date after which the request may be retried
     * @param string|null     $message    The internal exception message
     * @param \Throwable|null $previous   The previous exception
     * @param int|null        $code       The internal exception code
     */
    public function __construct(
        int|string|null $retryAfter = null,
        ?string $message = '',
        ?\Throwable $previous = null,
        ?int $code = 0,
        array $headers = []
    ) {
        if ($retryAfter) {
            $headers['Retry-After'] = $retryAfter;
        }

        parent::__construct(503, $message, $previous, $headers, $code);
    }
}
