<?php

namespace ZfeggTest\CallableHandlerDecorator\Factory;

use Composer\Autoload\ClassLoader;
use Laminas\ConfigAggregator\ConfigAggregator;
use Laminas\ServiceManager\AbstractFactory\ReflectionBasedAbstractFactory;
use Laminas\ServiceManager\ServiceManager;
use Mezzio\Application;
use Mezzio\MiddlewareFactory;
use Mezzio\Router\Middleware\DispatchMiddleware;
use Mezzio\Router\Middleware\RouteMiddleware;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Container\ContainerInterface;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Zfegg\CallableHandlerDecorator\ConfigProvider;
use Zfegg\CallableHandlerDecorator\Factory\RouteCollectorDecoratorDelegator;
use PHPUnit\Framework\TestCase;
use Zfegg\CallableHandlerDecorator\Router\RouteMetadata;
use Zfegg\ExpressiveTest\AbstractActionTestCase;
use ZfeggTest\CallableHandlerDecorator\AbstractTestCase;

class RouteCollectorDecoratorDelegatorTest extends AbstractTestCase
{

    public function testCall()
    {
        $this->get('/example/get')->assertNoContent();
    }
}
