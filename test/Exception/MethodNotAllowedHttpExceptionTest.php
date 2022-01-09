<?php

declare(strict_types = 1);

namespace ZfeggTest\PsrMvc\Exception;

use Zfegg\PsrMvc\Exception\HttpException;
use Zfegg\PsrMvc\Exception\MethodNotAllowedHttpException;

class MethodNotAllowedHttpExceptionTest extends HttpExceptionTest
{
    public function testHeadersDefault(): void
    {
        $exception = new MethodNotAllowedHttpException(['GET', 'PUT']);
        $this->assertSame(['Allow' => 'GET, PUT'], $exception->getHeaders());
    }

    public function testWithHeaderConstruct(): void
    {
        $headers = [
            'Cache-Control' => 'public, s-maxage=1200',
        ];

        $exception = new MethodNotAllowedHttpException(['get'], '', null, 0, $headers);

        $headers['Allow'] = 'GET';

        $this->assertSame($headers, $exception->getHeaders());
    }

    /**
     * @dataProvider headerDataProvider
     */
    public function testHeadersSetter(array $headers): void
    {
        $exception = new MethodNotAllowedHttpException(['GET']);
        $exception->setHeaders($headers);
        $this->assertSame($headers, $exception->getHeaders());
    }

    protected function createException(
        string $message = '',
        ?\Throwable $previous = null,
        ?int $code = null,
        array $headers = []
    ): HttpException {
        return new MethodNotAllowedHttpException(['get'], $message, $previous, $code, $headers);
    }
}
