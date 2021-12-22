<?php declare(strict_types = 1);

namespace Zfegg\PsrMvc\Container;

use Psr\Container\ContainerInterface;
use Zfegg\PsrMvc\CallbackHandler;
use Zfegg\PsrMvc\CallbackHandlerFactory as Factory;

class HandlerFactory
{
    public function __invoke(ContainerInterface $container, string $requestedName): CallbackHandler
    {
        return $container->get(Factory::class)->create($requestedName);
    }
}
