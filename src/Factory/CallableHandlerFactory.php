<?php declare(strict_types = 1);

namespace Zfegg\CallableHandlerDecorator\Factory;

use Psr\Container\ContainerInterface;
use Zfegg\CallableHandlerDecorator\CallableHandlerDecorator;
use Zfegg\CallableHandlerDecorator\ReflectionFactory;

class CallableHandlerFactory
{
    public function __invoke(ContainerInterface $container, string $requestedName): CallableHandlerDecorator
    {
        return $container->get(ReflectionFactory::class)->create($requestedName);
    }
}
