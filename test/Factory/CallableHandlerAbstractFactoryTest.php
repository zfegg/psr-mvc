<?php

namespace ZfeggTest\CallableHandlerDecorator\Factory;

use Laminas\ServiceManager\ServiceManager;
use Zfegg\CallableHandlerDecorator\CallableHandlerDecorator;
use Zfegg\CallableHandlerDecorator\Factory\CallableHandlerAbstractFactory;
use PHPUnit\Framework\TestCase;
use Zfegg\CallableHandlerDecorator\Factory\CallableHandlerFactory;
use Zfegg\CallableHandlerDecorator\Factory\ReflectionFactoryFactory;
use Zfegg\CallableHandlerDecorator\ReflectionFactory;
use ZfeggTest\CallableHandlerDecorator\Foo;

class CallableHandlerAbstractFactoryTest extends TestCase
{

    public function testInvoke()
    {
        $container = new ServiceManager([
            'invokables' => [Foo::class],
            'factories' => [
                ReflectionFactory::class => ReflectionFactoryFactory::class,
            ],
            'abstract_factories' => [
                CallableHandlerAbstractFactory::class,
            ]
        ]);
        $handler = $container->get(Foo::class . '@test');

        $this->assertFalse($container->has(Foo::class . '@notFound'));
        $this->assertInstanceOf(CallableHandlerDecorator::class, $handler);
    }
}
