<?php

declare(strict_types = 1);

namespace Zfegg\PsrMvc\ParamResolver;

use Psr\Container\ContainerInterface;
use ReflectionParameter;

class ParamFromContainer implements ParamResolverInterface
{
    private ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function has(string $name): bool
    {
        return $this->container->has($name);
    }

    public function resolve(object $attr, ReflectionParameter $parameter): callable
    {
        /** @var \Zfegg\PsrMvc\Attribute\FromContainer $attr */

        if (! $attr->name) {
            $name = $parameter->getType()->getName();
        } else {
            $name = $attr->name;
        }

        return fn() => $this->container->get($name);
    }
}
