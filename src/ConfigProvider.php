<?php

declare(strict_types = 1);

namespace Zfegg\PsrMvc;

use Mezzio\Router\RouteCollector;
use Zfegg\PsrMvc\Container\RouteCollectorInjectionDelegator;
use Zfegg\PsrMvc\Middleware\Middlewares;
use Zfegg\PsrMvc\Middleware\Serializer;
use Zfegg\PsrMvc\Route\RouteMetadata;

class ConfigProvider
{

    public function __invoke(): array
    {
        return [
            'dependencies'                => [
                'factories' => [
                    CallbackHandlerFactory::class => Container\CallbackHandlerFactoryFactory::class,
                    RouteMetadata::class          => Container\RouteMetadataFactory::class,
                    Middlewares::class            => Container\MiddlewaresFactory::class,
                    FormatMatcher::class          => Container\FormatMatcherFactory::class,
                ],
                'delegators' => [
                    RouteCollector::class => [
                        RouteCollectorInjectionDelegator::class,
                    ],
                ],
                'aliases' => [
                ]
            ],
            RouteMetadata::class => [
                'paths' => []
            ],
            CallbackHandlerFactory::class => [
                'defaultMiddlewares' => [
                    Serializer::class,
                ]
            ],
        ];
    }
}
