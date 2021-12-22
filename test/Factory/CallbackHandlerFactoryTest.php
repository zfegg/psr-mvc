<?php

declare(strict_types = 1);

namespace ZfeggTest\PsrMvc\Factory;

use Zfegg\PsrMvc\CallbackHandler;
use Zfegg\PsrMvc\Container\HandlerFactory;
use ZfeggTest\PsrMvc\AbstractTestCase;
use ZfeggTest\PsrMvc\Example\Foo;

class CallbackHandlerFactoryTest extends AbstractTestCase
{

    public function testInvoke(): void
    {
        $handler = (new HandlerFactory())($this->container, Foo::class . '@test');

        $this->assertInstanceOf(CallbackHandler::class, $handler);
    }

    public function testInvokeByServiceManager(): void
    {
        $this->container->setInvokableClass(Foo::class);
        $this->container->setFactory(Foo::class . '@test', HandlerFactory::class);
        $handler = $this->container->get(Foo::class . '@test');

        $this->assertInstanceOf(CallbackHandler::class, $handler);
    }
}
