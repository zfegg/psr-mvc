<?php

declare(strict_types = 1);

namespace Zfegg\PsrMvc\ParamResolver;

use Psr\Http\Message\ServerRequestInterface;
use ReflectionParameter;
use Zfegg\PsrMvc\Routing\ParameterConverterInterface;

class ParamFromAttribute implements ParamResolverInterface
{
    public function __construct(
        private ParameterConverterInterface $parameterConverter
    ) {
    }

    public function resolve(object $attr, ReflectionParameter $parameter): callable
    {
        /** @var \Zfegg\PsrMvc\Attribute\FromAttribute $attr */
        $name = $attr->name ?? $this->parameterConverter->convertParamToRequestParam($parameter->getName());

        $default = $parameter->isDefaultValueAvailable() ? $parameter->getDefaultValue() : null;
        return static fn(ServerRequestInterface $request) => $request->getAttribute($name, $default);
    }
}
