<?php

namespace ZfeggTest\PsrMvc\Middleware;

use Psr\Http\Message\ServerRequestInterface;
use Zfegg\PsrMvc\Middleware\JsonSerializer;
use PHPUnit\Framework\TestCase;

class JsonSerializerTest extends TestCase
{

    public function testProcess()
    {
        $serializer = new JsonSerializer();
        $res = $serializer->process(
            $this->createMock(ServerRequestInterface::class),
            fn() => [1, 2, 3]
        );

        $this->assertEquals([1, 2, 3], $res->getPayload());
    }
}
