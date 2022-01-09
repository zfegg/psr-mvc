<?php

declare(strict_types = 1);

namespace Zfegg\PsrMvc\Exception;

class HttpException extends \RuntimeException implements HttpExceptionInterface
{
    private int $statusCode;
    private array $headers;

    public function __construct(
        int $statusCode,
        ?string $message = '',
        ?\Throwable $previous = null,
        array $headers = [],
        ?int $code = 0
    ) {
        $this->statusCode = $statusCode;
        $this->headers = $headers;

        parent::__construct($message, $code, $previous);
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * Set response headers.
     *
     * @param array $headers Response headers
     */
    public function setHeaders(array $headers): void
    {
        $this->headers = $headers;
    }
}
