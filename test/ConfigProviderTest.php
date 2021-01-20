<?php

namespace ZfeggTest\CallableHandlerDecorator;

use Laminas\ServiceManager\ServiceManager;
use Zfegg\CallableHandlerDecorator\ConfigProvider;
use PHPUnit\Framework\TestCase;
use Zfegg\CallableHandlerDecorator\ReflectionFactory;

class ConfigProviderTest extends TestCase
{

    public function testInvoke()
    {
        $container = new ServiceManager((new ConfigProvider())()['dependencies']);

        $this->assertTrue($container->has(ReflectionFactory::class));
    }
}
