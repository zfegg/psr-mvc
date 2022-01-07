<?php

declare(strict_types = 1);

namespace Zfegg\PsrMvc\Container;

use Psr\Container\ContainerInterface;
use Zfegg\PsrMvc\Routing\RouteMetadata;

class RouteMetadataFactory
{

    public function __invoke(ContainerInterface $container): RouteMetadata
    {
        $config = $container->get('config')[RouteMetadata::class] ?? [];

        if (isset($config['parameterConverter']) && is_string($config['parameterConverter'])) {
            $config['parameterConverter'] = $container->get($config['parameterConverter']);
        }

        return new RouteMetadata(...$config);
    }
}
