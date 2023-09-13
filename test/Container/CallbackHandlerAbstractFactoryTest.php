<?php

declare(strict_types = 1);

namespace ZfeggTest\PsrMvc\Container;

use Laminas\ServiceManager\ServiceManager;
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

    public function testCycleInvoke(): void
    {
        $container = new ServiceManager();
        $container->addAbstractFactory(CallbackHandlerAbstractFactory::class);
        $this->assertFalse($container->has(Foo::class . '@test'));
    }
}
