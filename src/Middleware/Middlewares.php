<?php

namespace Zfegg\PsrMvc\Middleware;

use Laminas\ServiceManager\AbstractPluginManager;

class Middlewares extends AbstractPluginManager
{
    protected $instanceOf = MiddlewareInterface::class;

    protected $factories = [
        Serializer::class => SerializerFactory::class,
    ];
}