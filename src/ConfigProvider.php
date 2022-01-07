<?php

declare(strict_types = 1);

namespace Zfegg\PsrMvc;

use Mezzio\Router\RouteCollector;
use Zfegg\PsrMvc\Container\RouteCollectorInjectionDelegator;
use Zfegg\PsrMvc\Middleware\ContentTypeMiddleware;
use Zfegg\PsrMvc\ParamResolver\ParamResolverManager;
use Zfegg\PsrMvc\PrepareResponse\DefaultResponse;
use Zfegg\PsrMvc\PrepareResponse\PrepareResponseInterface;
use Zfegg\PsrMvc\PrepareResponse\SerializerResponse;
use Zfegg\PsrMvc\Routing\ParameterConverterInterface;
use Zfegg\PsrMvc\Routing\RouteMetadata;
use Zfegg\PsrMvc\Routing\SlugifyParameterConverter;

class ConfigProvider
{

    public function __invoke(): array
    {
        return [
            'dependencies'                => [
                'factories'  => [
                    CallbackHandlerFactory::class    => Container\CallbackHandlerFactoryFactory::class,
                    RouteMetadata::class             => Container\RouteMetadataFactory::class,
                    FormatMatcher::class             => Container\FormatMatcherFactory::class,
                    ParamResolverManager::class      => Container\ParamResolverManagerFactory::class,
                    ControllerHandler::class         => Container\ControllerHandlerFactory::class,
                    SlugifyParameterConverter::class => Container\SlugifyParameterConverterFactory::class,
                    DefaultResponse::class           => Container\DefaultResponseFactory::class,
                    SerializerResponse::class        => Container\SerializerResponseFactory::class,
                    ContentTypeMiddleware::class     => Container\ContentTypeMiddlewareFactory::class,
                ],
                'delegators' => [
                    RouteCollector::class => [
                        RouteCollectorInjectionDelegator::class,
                    ],
                ],
                'aliases'    => [
                    ParameterConverterInterface::class => SlugifyParameterConverter::class,
                    PrepareResponseInterface::class => DefaultResponse::class,
                ]
            ],
            RouteMetadata::class          => [
                'paths' => []
            ],
        ];
    }
}
