<?php declare(strict_types = 1);

namespace Zfegg\CallableHandlerDecorator;

use Laminas\ServiceManager\AbstractFactory\ReflectionBasedAbstractFactory;
use Laminas\ServiceManager\Factory\InvokableFactory;
use Mezzio\Router\RouteCollector;
use Zfegg\CallableHandlerDecorator\Factory\ReflectionFactoryFactory;
use Zfegg\CallableHandlerDecorator\Factory\RouteCollectorDecoratorDelegator;
use Zfegg\CallableHandlerDecorator\Middleware\Middlewares;
use Zfegg\CallableHandlerDecorator\Middleware\MiddlewaresFactory;
use Zfegg\CallableHandlerDecorator\Middleware\Serializer;
use Zfegg\CallableHandlerDecorator\Router\RouteMetadata;

class ConfigProvider
{

    public function __invoke(): array
    {
        return [
            'dependencies'       => [
                'factories' => [
                    ReflectionFactory::class => ReflectionFactoryFactory::class,
                    RouteMetadata::class     => Factory\MetadataProviderFactory::class,
                    Middlewares::class       => MiddlewaresFactory::class,
                    FormatMatcher::class     => Factory\FormatMatcherFactory::class,
                ],
                'delegators' => [
                    RouteCollector::class => [
                        RouteCollectorDecoratorDelegator::class,
                    ],
                ],
                'aliases' => [
                ]
            ],
            RouteMetadata::class => [
                'paths' => []
            ],
            ReflectionFactory::class => [
                'defaultMiddlewares' => [
                    Serializer::class,
                ]
            ],
        ];
    }
}
