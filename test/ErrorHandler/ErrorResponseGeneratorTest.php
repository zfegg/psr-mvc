<?php

declare(strict_types = 1);

namespace ZfeggTest\PsrMvc\ErrorHandler;

use Laminas\Diactoros\Response;
use Laminas\Diactoros\ServerRequestFactory;
use Mezzio\Middleware\ErrorResponseGenerator;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;
use Zfegg\PsrMvc\Exception\NotFoundHttpException;
use ZfeggTest\PsrMvc\AbstractTestCase;
use function PHPUnit\Framework\assertArrayHasKey;
use function PHPUnit\Framework\assertEquals;

class ErrorResponseGeneratorTest extends AbstractTestCase
{

    public function invokeResponseJsonData(): array
    {
        $request = (new ServerRequestFactory())
            ->createServerRequest("GET", "/");
        $notFoundEx = new NotFoundHttpException("Not found", headers: ['x-foo' => 'Foo']);
        return [
            [
                $notFoundEx,
                $request->withHeader('x-requested-with', 'XMLHttpRequest'),
                404,
            ],
            [
                $notFoundEx,
                $request->withAttribute('format', 'jsonld'),
                404,
            ],
            [
                new \Exception("Foo"),
                $request->withAttribute('format', 'json'),
                500,
                "Server Error",
            ],
            [
                $notFoundEx,
                $request->withHeader('x-requested-with', 'XMLHttpRequest'),
                404,
                null,
                true,
            ],
            [
                $notFoundEx,
                $request->withAttribute('format', 'jsonld'),
                404,
                null,
                true,
            ],
            [
                new \Exception("Foo"),
                $request->withAttribute('format', 'json'),
                500,
                null,
                true,
            ],
        ];
    }


    /**
     * @dataProvider invokeResponseJsonData
     */
    public function testInvokeResponseJson(
        Throwable $e,
        ServerRequestInterface $request,
        int $statusCode,
        ?string $msg = null,
        bool $debug = false,
    ): void {
        $config = $this->container->get('config');
        $config['debug'] = $debug;
        $this->container->setService('config', $config);
        $generator = $this->container->get(ErrorResponseGenerator::class);

        /** @var \Psr\Http\Message\ResponseInterface $response */
        $response = $generator(
            $e,
            $request,
            new Response(),
        );

        assertEquals($statusCode, $response->getStatusCode());
        $body = (string)$response->getBody();
        $result = json_decode($body, true);

        assertEquals($e->getCode(), $result['code']);
        assertEquals($msg ?: $e->getMessage(), $result['message']);
        if ($debug) {
            assertArrayHasKey('exception', $result);
            assertArrayHasKey('file', $result);
            assertArrayHasKey('line', $result);
            assertArrayHasKey('trace', $result);
        }
    }


    public function invokeResponseTextData(): array
    {
        $request = (new ServerRequestFactory())
            ->createServerRequest("GET", "/");
        $notFoundEx = new NotFoundHttpException(
            "Not found",
            null,
            1000,
            ['x-foo' => 'Foo']
        );
        return [
            [
                $notFoundEx,
                $request,
                404,
            ],
            [
                new \Exception("Foo"),
                $request,
                500,
            ],
            [
                $notFoundEx,
                $request,
                404,
                true,
            ],
            [
                new \Exception("Foo"),
                $request,
                500,
                true,
            ],
        ];
    }

    /**
     * @dataProvider invokeResponseTextData
     */
    public function testInvokeResponseText(
        Throwable $e,
        ServerRequestInterface $request,
        int $statusCode,
        bool $debug = false,
    ): void {
        $config = $this->container->get('config');
        $config['debug'] = $debug;
        $this->container->setService('config', $config);
        $generator = $this->container->get(ErrorResponseGenerator::class);

        /** @var \Psr\Http\Message\ResponseInterface $response */
        $response = $generator(
            $e,
            $request,
            new Response(),
        );

        assertEquals($statusCode, $response->getStatusCode());
        $body = (string)$response->getBody();

        self::assertStringStartsWith("An unexpected error occurred", $body);
        if ($debug) {
            self::assertStringContainsString("stack trace", $body);
        }
    }
}
