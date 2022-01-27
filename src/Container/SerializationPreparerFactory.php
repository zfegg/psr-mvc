<?php

declare(strict_types = 1);

namespace Zfegg\PsrMvc\Container;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Zfegg\PsrMvc\FormatMatcher;
use Zfegg\PsrMvc\Preparer\SerializationPreparer;

class SerializationPreparerFactory
{
    public function __invoke(ContainerInterface $container): SerializationPreparer
    {
        return new SerializationPreparer(
            $container->get(FormatMatcher::class),
            $container->get(SerializerInterface::class),
            $container->get(ResponseFactoryInterface::class),
        );
    }
}
