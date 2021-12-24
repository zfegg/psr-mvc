<?php

declare(strict_types = 1);

namespace Zfegg\PsrMvc\Container;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\AbstractFactoryInterface;
use Zfegg\PsrMvc\CallbackHandlerFactory;

class CallbackHandlerAbstractFactory implements AbstractFactoryInterface
{
    /**
     * @inheritDoc
     */
    public function canCreate(ContainerInterface $container, $requestedName)
    {
        return $container->get(CallbackHandlerFactory::class)->exists($requestedName);
    }

    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        return $container->get(CallbackHandlerFactory::class)->create($requestedName);
    }
}
