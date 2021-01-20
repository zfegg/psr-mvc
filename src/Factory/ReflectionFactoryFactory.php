<?php


namespace Zfegg\CallableHandlerDecorator\Factory;


use Psr\Container\ContainerInterface;
use Zfegg\CallableHandlerDecorator\ReflectionFactory;

class ReflectionFactoryFactory
{
    public function __invoke(ContainerInterface $container)
    {
        return new ReflectionFactory(
            $container,
            function (string $name): string {
                return preg_replace_callback('/([A-Z]+)/', function ($word) {
                    return '_' . strtolower($word[1]);
                }, $name);
            }
        );
    }
}
