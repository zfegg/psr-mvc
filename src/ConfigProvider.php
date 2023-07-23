<?php

declare(strict_types = 1);

namespace Zfegg\PsrMvc;

use Mezzio\Router\RouteCollector;
use Zfegg\PsrMvc\Container\RouteCollectorInjectionDelegator;
use Zfegg\PsrMvc\ErrorHandler\ErrorResponseGenerator;
use Zfegg\PsrMvc\Middleware\ContentTypeMiddleware;
use Zfegg\PsrMvc\ParamResolver\ParamResolverManager;
use Zfegg\PsrMvc\Preparer\CommonPreparer;
use Zfegg\PsrMvc\Preparer\DefaultPreparer;
use Zfegg\PsrMvc\Preparer\PreparerStack;
use Zfegg\PsrMvc\Preparer\ResultPreparableInterface;
use Zfegg\PsrMvc\Preparer\SerializationPreparer;
use Zfegg\PsrMvc\Routing\ParameterConverterInterface;
use Zfegg\PsrMvc\Routing\RouteMetadata;
use Zfegg\PsrMvc\Routing\SlugifyParameterConverter;

class ConfigProvider
{

    public function __invoke(): array
    {
        return [
            'dependencies' => [
                'factories' => [
                    ErrorResponseGenerator::class => Container\ErrorResponseGeneratorFactory::class,
                    CallbackHandlerFactory::class => Container\CallbackHandlerFactoryFactory::class,
                    RouteMetadata::class => Container\RouteMetadataFactory::class,
                    FormatMatcher::class => Container\FormatMatcherFactory::class,
                    ParamResolverManager::class => Container\ParamResolverManagerFactory::class,
                    ControllerHandler::class => Container\ControllerHandlerFactory::class,
                    SlugifyParameterConverter::class => Container\SlugifyParameterConverterFactory::class,
                    PreparerStack::class => Container\PreparerStackFactory::class,
                    CommonPreparer::class => Container\InvokableFactory::class,
                    DefaultPreparer::class => Container\InvokableFactory::class,
                    SerializationPreparer::class => Container\SerializationPreparerFactory::class,
                    ContentTypeMiddleware::class => Container\ContentTypeMiddlewareFactory::class,
                ],
                'delegators' => [
                    RouteCollector::class => [
                        RouteCollectorInjectionDelegator::class,
                    ],
                ],
                'aliases' => [
                    ParameterConverterInterface::class => SlugifyParameterConverter::class,
                    ResultPreparableInterface::class => PreparerStack::class,
                ]
            ],
            RouteMetadata::class => [
                'paths' => []
            ],
        ];
    }
}
