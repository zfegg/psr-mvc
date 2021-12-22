<?php


namespace Zfegg\CallableHandlerDecorator\Factory;

use Psr\Container\ContainerInterface;
use Zfegg\CallableHandlerDecorator\Router\RouteMetadata;

class MetadataProviderFactory
{

    public function __invoke(ContainerInterface $container)
    {
        $config = $container->get('config')[RouteMetadata::class] ?? [];

        $paths = $config['paths'] ?? [];
        $excludePaths = $config['excludePaths'] ?? [];

        return new RouteMetadata(
            $paths,
            $excludePaths
        );
    }
}