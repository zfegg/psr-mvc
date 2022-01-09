<?php

declare(strict_types = 1);

namespace ZfeggTest\PsrMvc\Exception;

use Zfegg\PsrMvc\Exception\HttpException;
use Zfegg\PsrMvc\Exception\UnauthorizedHttpException;

class UnauthorizedHttpExceptionTest extends HttpExceptionTest
{
    public function testHeadersDefault(): void
    {
        $exception = new UnauthorizedHttpException('Challenge');
        $this->assertSame(['WWW-Authenticate' => 'Challenge'], $exception->getHeaders());
    }

    public function testWithHeaderConstruct(): void
    {
        $headers = [
            'Cache-Control' => 'public, s-maxage=1200',
        ];

        $exception = new UnauthorizedHttpException('Challenge', '', null, 0, $headers);

        $headers['WWW-Authenticate'] = 'Challenge';

        $this->assertSame($headers, $exception->getHeaders());
    }

    /**
     * @dataProvider headerDataProvider
     */
    public function testHeadersSetter(array $headers): void
    {
        $exception = new UnauthorizedHttpException('Challenge');
        $exception->setHeaders($headers);
        $this->assertSame($headers, $exception->getHeaders());
    }

    protected function createException(
        string $message = '',
        ?\Throwable $previous = null,
        ?int $code = null,
        array $headers = []
    ): HttpException {
        return new UnauthorizedHttpException('Challenge', $message, $previous, $code, $headers);
    }
}
