<?php declare(strict_types = 1);

namespace Zfegg\CallableHandlerDecorator\Factory;

use Psr\Container\ContainerInterface;
use Zfegg\CallableHandlerDecorator\Middleware\Middlewares;
use Zfegg\CallableHandlerDecorator\Middleware\Serializer;
use Zfegg\CallableHandlerDecorator\ReflectionFactory;
use Zfegg\CallableHandlerDecorator\Utils\Word;

class ReflectionFactoryFactory
{
    public function __invoke(ContainerInterface $container): ReflectionFactory
    {
        return new ReflectionFactory(
            $container,
            [Word::class, 'tableize'],
            [$container->get(Middlewares::class)->get(Serializer::class)]
        );
    }
}
