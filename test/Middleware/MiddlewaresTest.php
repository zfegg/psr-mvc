<?php

namespace ZfeggTest\PsrMvc\Middleware;

use Zfegg\PsrMvc\Middleware\Middlewares;
use Zfegg\PsrMvc\Middleware\Serializer;
use ZfeggTest\PsrMvc\AbstractTestCase;

class MiddlewaresTest extends AbstractTestCase
{

    public function testGet()
    {
        $middlewares = $this->container->get(Middlewares::class);
        $m = $middlewares->get(Serializer::class, ['context' => ['']]);

        $this->assertInstanceOf(Serializer::class, $m);
    }
}
