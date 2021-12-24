<?php

declare(strict_types = 1);

namespace ZfeggTest\PsrMvc\Middleware;

use Laminas\Diactoros\ServerRequestFactory;
use Psr\Http\Message\ResponseInterface;
use Zfegg\PsrMvc\Middleware\Middlewares;
use Zfegg\PsrMvc\Middleware\Serializer;
use ZfeggTest\PsrMvc\AbstractTestCase;

class SerializerTest extends AbstractTestCase
{

    public function testProcess(): void
    {
        $request = (new ServerRequestFactory())->createServerRequest('GET', '/')->withAttribute('format', 'json');
        $middlewares = $this->container->get(Middlewares::class);
        $m = $middlewares->get(Serializer::class, ['context' => ['']]);
        $response = $m->process($request, fn() => [1,2 ,3]);

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertEquals('application/json', $response->getHeaderLine('Content-Type'));
        $this->assertEquals('[1,2,3]', $response->getBody() . '');
    }
}
