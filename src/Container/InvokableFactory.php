<?php

namespace Zfegg\PsrMvc\Container;

use Psr\Container\ContainerInterface;

class InvokableFactory
{

    public function __invoke(ContainerInterface $container, string $requestedName): object
    {
        return new $requestedName;
    }
}