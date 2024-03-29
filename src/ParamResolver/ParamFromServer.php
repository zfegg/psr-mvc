<?php

declare(strict_types = 1);

namespace Zfegg\PsrMvc\ParamResolver;

use Psr\Http\Message\ServerRequestInterface;
use ReflectionParameter;

class ParamFromServer implements ParamResolverInterface
{
    public function resolve(object $attr, ReflectionParameter $parameter): callable
    {
        /** @var \Zfegg\PsrMvc\Attribute\FromBody $attr */
        $name = $attr->name ?? $parameter->getName();
        $default = $parameter->isDefaultValueAvailable() ? $parameter->getDefaultValue() : null;
        return static fn(ServerRequestInterface $request): mixed => $request->getServerParams()[$name] ?? $default;
    }
}
