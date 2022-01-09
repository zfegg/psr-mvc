<?php

declare(strict_types = 1);

namespace ZfeggTest\PsrMvc\Exception;

use Zfegg\PsrMvc\Exception\HttpException;
use Zfegg\PsrMvc\Exception\ServiceUnavailableHttpException;

class ServiceUnavailableHttpExceptionTest extends HttpExceptionTest
{
    public function testHeadersDefaultRetryAfter(): void
    {
        $exception = new ServiceUnavailableHttpException(10);
        $this->assertSame(['Retry-After' => 10], $exception->getHeaders());
    }

    public function testWithHeaderConstruct(): void
    {
        $headers = [
            'Cache-Control' => 'public, s-maxage=1337',
        ];

        $exception = new ServiceUnavailableHttpException(1337, '', null, 0, $headers);

        $headers['Retry-After'] = 1337;

        $this->assertSame($headers, $exception->getHeaders());
    }

    /**
     * @dataProvider headerDataProvider
     */
    public function testHeadersSetter(array $headers): void
    {
        $exception = new ServiceUnavailableHttpException(10);
        $exception->setHeaders($headers);
        $this->assertSame($headers, $exception->getHeaders());
    }

    protected function createException(
        string $message = '',
        ?\Throwable $previous = null,
        int $code = 0,
        array $headers = []
    ): HttpException {
        return new ServiceUnavailableHttpException(null, $message, $previous, $code, $headers);
    }
}
