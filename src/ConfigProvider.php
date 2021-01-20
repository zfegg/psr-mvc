<?php

namespace Zfegg\CallableHandlerDecorator;

use Zfegg\CallableHandlerDecorator\Factory\ReflectionFactoryFactory;

class ConfigProvider
{

    public function __invoke()
    {
        return [
            'dependencies' => [
                'factories' => [
                    ReflectionFactory::class => ReflectionFactoryFactory::class,
                ]
            ]
        ];
    }
}