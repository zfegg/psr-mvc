<?php

namespace Zfegg\CallableHandlerDecorator\Factory;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\AbstractFactoryInterface;
use Zfegg\CallableHandlerDecorator\ReflectionFactory;

class CallableHandlerAbstractFactory implements AbstractFactoryInterface
{
    public function canCreate(ContainerInterface $container, $requestedName)
    {
        return $container->get(ReflectionFactory::class)->exists($requestedName);
    }

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return $container->get(ReflectionFactory::class)->create($requestedName);
    }
}
