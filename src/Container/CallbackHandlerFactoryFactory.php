<?php

declare(strict_types = 1);

namespace Zfegg\PsrMvc\Container;

use Psr\Container\ContainerInterface;
use Zfegg\PsrMvc\CallbackHandlerFactory;
use Zfegg\PsrMvc\ParamResolver\ParamResolverManager;
use Zfegg\PsrMvc\Preparer\ResultPreparableInterface;

class CallbackHandlerFactoryFactory
{
    public function __invoke(ContainerInterface $container): CallbackHandlerFactory
    {
        $config = $container->get('config')[CallbackHandlerFactory::class] ?? [];

        return new CallbackHandlerFactory(
            $container,
            $container->get(ParamResolverManager::class),
            $container->get(ResultPreparableInterface::class),
            ...$config,
        );
    }
}
