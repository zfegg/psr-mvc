<?php

namespace Zfegg\PsrMvc\Middleware;

use Psr\Container\ContainerInterface;

class MiddlewaresFactory
{

    public function __invoke(ContainerInterface $container)
    {
        return new Middlewares($container, $container->get('config')[Middlewares::class] ?? []);
    }
}