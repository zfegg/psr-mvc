<?php

declare(strict_types = 1);

namespace Zfegg\PsrMvc\Utils;

use Psr\Http\Message\ResponseInterface;

class ResponseUtils
{

    public static function toArray(ResponseInterface $response): mixed
    {
        return json_decode((string)$response->getBody(), true);
    }


    /**
     * Is response informative?
     *
     * @final
     */
    public static function isInformational(ResponseInterface $response): bool
    {
        $statusCode = $response->getStatusCode();
        return $statusCode >= 100 && $statusCode < 200;
    }

    /**
     * Is response successful?
     *
     * @final
     */
    public static function isSuccessful(ResponseInterface $response): bool
    {
        $statusCode = $response->getStatusCode();
        return $statusCode >= 200 && $statusCode < 300;
    }

    /**
     * Is the response a redirect?
     *
     * @final
     */
    public static function isRedirection(ResponseInterface $response): bool
    {
        $statusCode = $response->getStatusCode();
        return $statusCode >= 300 && $statusCode < 400;
    }

    /**
     * Is there a client error?
     *
     * @final
     */
    public static function isClientError(ResponseInterface $response): bool
    {
        $statusCode = $response->getStatusCode();
        return $statusCode >= 400 && $statusCode < 500;
    }

    /**
     * Was there a server side error?
     *
     * @final
     */
    public static function isServerError(ResponseInterface $response): bool
    {
        $statusCode = $response->getStatusCode();
        return $statusCode >= 500 && $statusCode < 600;
    }

    /**
     * Is the response OK?
     *
     * @final
     */
    public static function isOk(ResponseInterface $response): bool
    {
        $statusCode = $response->getStatusCode();
        return 200 === $statusCode;
    }

    /**
     * Is the response forbidden?
     *
     * @final
     */
    public static function isForbidden(ResponseInterface $response): bool
    {
        $statusCode = $response->getStatusCode();
        return 403 === $statusCode;
    }

    /**
     * Is the response a not found error?
     *
     * @final
     */
    public static function isNotFound(ResponseInterface $response): bool
    {
        $statusCode = $response->getStatusCode();
        return 404 === $statusCode;
    }

    /**
     * Is the response a redirect of some form?
     *
     * @final
     */
    public static function isRedirect(ResponseInterface $response, ?string $location = null): bool
    {
        $statusCode = $response->getStatusCode();
        return \in_array($statusCode, [201, 301, 302, 303, 307, 308])
            && (null === $location || $location == $response->getHeaderLine('Location'));
    }

    /**
     * Is the response empty?
     *
     * @final
     */
    public static function isEmpty(ResponseInterface $response): bool
    {
        $statusCode = $response->getStatusCode();
        return \in_array($statusCode, [204, 304]);
    }
}
