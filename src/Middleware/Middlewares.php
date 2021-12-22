<?php

declare(strict_types = 1);

namespace Zfegg\PsrMvc\Middleware;

use Laminas\ServiceManager\AbstractPluginManager;

class Middlewares extends AbstractPluginManager
{
    /** @inheritDoc  */
    protected $instanceOf = MiddlewareInterface::class;

    /** @inheritDoc  */
    protected $factories = [
        Serializer::class => SerializerFactory::class,
    ];
}
