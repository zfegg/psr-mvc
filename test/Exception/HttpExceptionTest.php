<?php

declare(strict_types = 1);

namespace ZfeggTest\PsrMvc\Exception;

use PHPUnit\Framework\TestCase;
use Zfegg\PsrMvc\Exception\HttpException;

class HttpExceptionTest extends TestCase
{
    public function headerDataProvider(): array
    {
        return [
            [['X-Test' => 'Test']],
            [['X-Test' => 1]],
            [
                [
                    ['X-Test' => 'Test'],
                    ['X-Test-2' => 'Test-2'],
                ],
            ],
        ];
    }

    public function testHeadersDefault(): void
    {
        $exception = $this->createException();
        $this->assertSame([], $exception->getHeaders());
    }

    /**
     * @dataProvider headerDataProvider
     */
    public function testHeadersConstructor(array $headers): void
    {
        $exception = new HttpException(200, '', null, $headers);
        $this->assertSame($headers, $exception->getHeaders());
    }

    /**
     * @dataProvider headerDataProvider
     */
    public function testHeadersSetter(array $headers): void
    {
        $exception = $this->createException();
        $exception->setHeaders($headers);
        $this->assertSame($headers, $exception->getHeaders());
    }

    public function testThrowableIsAllowedForPrevious(): void
    {
        $previous = new class('Error of PHP 7+') extends \Error {
        };
        $exception = $this->createException('', $previous);
        $this->assertSame($previous, $exception->getPrevious());
    }

    protected function createException(
        string $message = '',
        ?\Throwable $previous = null,
        int $code = 0,
        array $headers = []
    ): HttpException {
        return new HttpException(200, $message, $previous, $headers, $code);
    }
}
