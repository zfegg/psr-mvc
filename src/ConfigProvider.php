<?php declare(strict_types = 1);

namespace Zfegg\CallableHandlerDecorator;

use Zfegg\CallableHandlerDecorator\Factory\ReflectionFactoryFactory;

class ConfigProvider
{

    public function __invoke(): array
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
