<?php

declare(strict_types = 1);

namespace Zfegg\PsrMvc\Container;

use Psr\Container\ContainerInterface;
use Zfegg\PsrMvc\Route\RouteMetadata;

class RouteMetadataFactory
{

    public function __invoke(ContainerInterface $container): RouteMetadata
    {
        $config = $container->get('config')[RouteMetadata::class] ?? [];

        if (isset($config['parameterTransformer']) && is_string($config['parameterTransformer'])) {
            $config['parameterTransformer'] = $container->get($config['parameterTransformer']);
        }

        return new RouteMetadata(...$config);
    }
}
