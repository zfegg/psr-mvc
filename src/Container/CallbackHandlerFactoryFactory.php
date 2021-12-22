<?php

declare(strict_types = 1);

namespace Zfegg\PsrMvc\Container;

use Psr\Container\ContainerInterface;
use Zfegg\PsrMvc\Middleware\Middlewares;
use Zfegg\PsrMvc\Middleware\Serializer;
use Zfegg\PsrMvc\CallbackHandlerFactory;
use Zfegg\PsrMvc\Utils\Word;

class CallbackHandlerFactoryFactory
{
    public function __invoke(ContainerInterface $container): CallbackHandlerFactory
    {
        $config = $container->get('config')[CallbackHandlerFactory::class] ?? [];

        $config['paramNameTransformer'] = $config['paramNameTransformer'] ?? [Word::class, 'tableize'];

        if (isset($config['defaultMiddlewares'])) {
            foreach ($config['defaultMiddlewares'] as $key => &$middleware) {
                if (is_string($key)) {
                    $options = $middleware;
                    $middleware = $key;
                }
                if (is_string($middleware)) {
                    $middleware = $container->get(Middlewares::class)->get(Serializer::class, $options ?? []);
                }
            }
        } else {
            // Default `Serializer` middleware.
            $config['defaultMiddlewares'] = [$container->get(Middlewares::class)->get(Serializer::class)];
        }

        return new CallbackHandlerFactory(
            $container,
            ...$config,
        );
    }
}
