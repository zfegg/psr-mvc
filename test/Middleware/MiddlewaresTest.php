<?php

declare(strict_types = 1);

namespace ZfeggTest\PsrMvc\Middleware;

use Zfegg\PsrMvc\Middleware\Middlewares;
use Zfegg\PsrMvc\Middleware\Serializer;
use ZfeggTest\PsrMvc\AbstractTestCase;

class MiddlewaresTest extends AbstractTestCase
{

    public function testGet(): void
    {
        $middlewares = $this->container->get(Middlewares::class);
        $m = $middlewares->get(Serializer::class, ['context' => ['']]);

        $this->assertInstanceOf(Serializer::class, $m);
    }
}
