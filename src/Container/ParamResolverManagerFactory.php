<?php

declare(strict_types = 1);

namespace Zfegg\PsrMvc\Container;

use Psr\Container\ContainerInterface;
use Zfegg\PsrMvc\ParamResolver\ParamResolverManager;

class ParamResolverManagerFactory
{

    public function __invoke(ContainerInterface $container): ParamResolverManager
    {
        return new ParamResolverManager(
            $container,
            $container->get('config')[ParamResolverManager::class] ?? []
        );
    }
}
