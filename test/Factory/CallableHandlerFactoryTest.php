<?php declare(strict_types = 1);

namespace ZfeggTest\CallableHandlerDecorator\Factory;

use Laminas\ServiceManager\ServiceManager;
use Zfegg\CallableHandlerDecorator\CallableHandlerDecorator;
use Zfegg\CallableHandlerDecorator\Factory\CallableHandlerFactory;
use PHPUnit\Framework\TestCase;
use Zfegg\CallableHandlerDecorator\Factory\ReflectionFactoryFactory;
use Zfegg\CallableHandlerDecorator\ReflectionFactory;
use ZfeggTest\CallableHandlerDecorator\Foo;

class CallableHandlerFactoryTest extends TestCase
{

    public function testInvoke(): void
    {
        $container = new ServiceManager([
            'invokables' => [Foo::class],
            'factories' => [
                ReflectionFactory::class => ReflectionFactoryFactory::class,
            ]
        ]);
        $handler = (new CallableHandlerFactory())($container, Foo::class . '@test');

        $this->assertInstanceOf(CallableHandlerDecorator::class, $handler);
    }

    public function testInvokeByServiceManager(): void
    {
        $container = new ServiceManager([
            'invokables' => [Foo::class],
            'factories' => [
                ReflectionFactory::class => ReflectionFactoryFactory::class,
                Foo::class . '@test' => CallableHandlerFactory::class,
            ],
        ]);
        $handler = $container->get(Foo::class . '@test');
        $this->assertInstanceOf(CallableHandlerDecorator::class, $handler);
    }
}
