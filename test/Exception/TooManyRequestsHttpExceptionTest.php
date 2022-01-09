<?php

declare(strict_types = 1);

namespace ZfeggTest\PsrMvc\Exception;

use Zfegg\PsrMvc\Exception\HttpException;
use Zfegg\PsrMvc\Exception\TooManyRequestsHttpException;

class TooManyRequestsHttpExceptionTest extends HttpExceptionTest
{
    public function testHeadersDefaultRertyAfter(): void
    {
        $exception = new TooManyRequestsHttpException(10);
        $this->assertSame(['Retry-After' => 10], $exception->getHeaders());
    }

    public function testWithHeaderConstruct(): void
    {
        $headers = [
            'Cache-Control' => 'public, s-maxage=69',
        ];

        $exception = new TooManyRequestsHttpException(69, '', null, 0, $headers);

        $headers['Retry-After'] = 69;

        $this->assertSame($headers, $exception->getHeaders());
    }

    /**
     * @dataProvider headerDataProvider
     */
    public function testHeadersSetter(array $headers): void
    {
        $exception = new TooManyRequestsHttpException(10);
        $exception->setHeaders($headers);
        $this->assertSame($headers, $exception->getHeaders());
    }

    protected function createException(
        string $message = '',
        ?\Throwable $previous = null,
        ?int $code = null,
        array $headers = []
    ): HttpException {
        return new TooManyRequestsHttpException(null, $message, $previous, $code, $headers);
    }
}
