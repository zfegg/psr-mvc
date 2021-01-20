<?php


namespace Zfegg\CallableHandlerDecorator\Factory;


use Psr\Container\ContainerInterface;
use Zfegg\CallableHandlerDecorator\ReflectionFactory;

class CallableHandlerFactory
{
    public function __invoke(ContainerInterface $container, string $requestedName)
    {
        return $container->get(ReflectionFactory::class)->create($requestedName);
    }
}
