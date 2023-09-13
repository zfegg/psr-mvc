<?php

declare(strict_types = 1);

namespace Zfegg\PsrMvc;

use Mezzio\Middleware\ErrorResponseGenerator;
use Mezzio\Router\RouteCollector;
use Zfegg\PsrMvc\Container\RouteCollectorInjectionDelegator;
use Zfegg\PsrMvc\Preparer\PreparerStack;
use Zfegg\PsrMvc\Preparer\ResultPreparableInterface;
use Zfegg\PsrMvc\Routing\ParameterConverterInterface;
use Zfegg\PsrMvc\Routing\SlugifyParameterConverter;

class ConfigProvider
{

    public function __invoke(): array
    {
        return [
            'dependencies' => [
                'factories' => [
                    ErrorResponseGenerator::class => Container\ErrorResponseGeneratorFactory::class,
                    PreparerStack::class => Container\PreparerStackFactory::class,
                ],
                'delegators' => [
                    RouteCollector::class => [
                        RouteCollectorInjectionDelegator::class,
                    ],
                ],
                'aliases' => [
                    ParameterConverterInterface::class => SlugifyParameterConverter::class,
                    ResultPreparableInterface::class => PreparerStack::class,
                ],
                'auto' => [
                    'types' => [
                        ControllerHandler::class => [
                            'parameters' => [
                                'notFoundHandler' => \Mezzio\Handler\NotFoundHandler::class,
                            ]
                        ],
                    ]
                ],
            ],
        ];
    }
}
