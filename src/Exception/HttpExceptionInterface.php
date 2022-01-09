<?php

declare(strict_types = 1);

namespace Zfegg\PsrMvc\Exception;

/**
 * Interface for HTTP error exceptions.
 */
interface HttpExceptionInterface extends \Throwable
{
    /**
     * Returns the status code.
     *
     * @return int An HTTP response status code
     */
    public function getStatusCode(): int;

    /**
     * Returns response headers.
     *
     * @return array Response headers
     */
    public function getHeaders(): array;
}
