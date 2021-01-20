<?php declare(strict_types = 1);

namespace ZfeggTest\CallableHandlerDecorator;

use Laminas\ServiceManager\ServiceManager;
use Zfegg\CallableHandlerDecorator\ConfigProvider;
use PHPUnit\Framework\TestCase;
use Zfegg\CallableHandlerDecorator\ReflectionFactory;

class ConfigProviderTest extends TestCase
{

    public function testInvoke(): void
    {
        $container = new ServiceManager((new ConfigProvider())()['dependencies']);

        $this->assertTrue($container->has(ReflectionFactory::class));
    }
}
