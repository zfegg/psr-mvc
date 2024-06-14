<?php

declare(strict_types = 1);

namespace Zfegg\PsrMvc\Container;

use Laminas\ServiceManager\Exception\ServiceNotFoundException;
use Laminas\ServiceManager\Factory\AbstractFactoryInterface;
use Psr\Container\ContainerInterface;
use Zfegg\PsrMvc\CallbackHandlerFactory;

class CallbackHandlerAbstractFactory implements AbstractFactoryInterface
{
    /**
     * @inheritDoc
     */
    public function canCreate(ContainerInterface $container, $requestedName): bool
    {
        // For resolve cycle create.
        if ($requestedName === CallbackHandlerFactory::class) {
            return false;
        }
        try {
            return $container->get(CallbackHandlerFactory::class)->exists($requestedName);
        } catch (ServiceNotFoundException $e) {
            return false;
        }
    }

    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): mixed
    {
        return $container->get(CallbackHandlerFactory::class)->create($requestedName);
    }
}
