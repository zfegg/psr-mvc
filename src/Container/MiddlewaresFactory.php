<?php

declare(strict_types = 1);

namespace Zfegg\PsrMvc\Container;

use Psr\Container\ContainerInterface;
use Zfegg\PsrMvc\Middleware\Middlewares;

class MiddlewaresFactory
{

    public function __invoke(ContainerInterface $container): Middlewares
    {
        return new Middlewares($container, $container->get('config')[Middlewares::class] ?? []);
    }
}
