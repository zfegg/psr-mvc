<?php

declare(strict_types = 1);

namespace ZfeggTest\PsrMvc\Container;

use Zfegg\PsrMvc\CallbackHandler;
use Zfegg\PsrMvc\Container\CallbackHandlerAbstractFactory;
use ZfeggTest\PsrMvc\AbstractTestCase;
use ZfeggTest\PsrMvc\Example\Foo;

class CallbackHandlerAbstractFactoryTest extends AbstractTestCase
{

    public function testInvoke(): void
    {
        $container = $this->container;
        $container->addAbstractFactory(CallbackHandlerAbstractFactory::class);
        $handler = $container->get(Foo::class . '@test');

        $this->assertFalse($container->has(Foo::class . '@notFound'));
        $this->assertInstanceOf(CallbackHandler::class, $handler);
    }
}
