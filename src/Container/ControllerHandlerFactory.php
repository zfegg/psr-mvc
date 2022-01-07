<?php

declare(strict_types = 1);

namespace Zfegg\PsrMvc\Container;

use Mezzio\Handler\NotFoundHandler;
use Psr\Container\ContainerInterface;
use Zfegg\PsrMvc\CallbackHandlerFactory;
use Zfegg\PsrMvc\ControllerHandler;
use Zfegg\PsrMvc\Routing\ParameterConverterInterface;

class ControllerHandlerFactory
{

    public function __invoke(ContainerInterface $container): ControllerHandler
    {
        return new ControllerHandler(
            $container->get(CallbackHandlerFactory::class),
            $container->get(ParameterConverterInterface::class),
            $container->get(NotFoundHandler::class),
        );
    }
}
