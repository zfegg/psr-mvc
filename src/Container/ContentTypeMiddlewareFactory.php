<?php

namespace Zfegg\PsrMvc\Container;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Zfegg\PsrMvc\FormatMatcher;
use Zfegg\PsrMvc\Middleware\ContentTypeMiddleware;

class ContentTypeMiddlewareFactory
{

    public function __invoke(ContainerInterface $container): ContentTypeMiddleware
    {
        return new ContentTypeMiddleware(
            $container->get(FormatMatcher::class),
            $container->get(ResponseFactoryInterface::class),
        );
    }
}