<?php

declare(strict_types = 1);

namespace Zfegg\PsrMvc\Container;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Zfegg\PsrMvc\FormatMatcher;
use Zfegg\PsrMvc\PrepareResponse\SerializerResponse;

class SerializerResponseFactory
{
    public function __invoke(ContainerInterface $container): SerializerResponse
    {
        return new SerializerResponse(
            $container->get(FormatMatcher::class),
            $container->get(SerializerInterface::class),
            $container->get(ResponseFactoryInterface::class),
        );
    }
}
