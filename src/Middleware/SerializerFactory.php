<?php

declare(strict_types = 1);

namespace Zfegg\PsrMvc\Middleware;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Zfegg\PsrMvc\FormatMatcher;

class SerializerFactory implements FactoryInterface
{

    /** @inheritdoc  */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        return new Serializer(
            $container->get(FormatMatcher::class),
            $container->get(SerializerInterface::class),
            $container->get(ResponseFactoryInterface::class),
            $options ?? [],
        );
    }
}
