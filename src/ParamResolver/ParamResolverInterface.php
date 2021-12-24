<?php

declare(strict_types = 1);

namespace Zfegg\PsrMvc\ParamResolver;

use ReflectionParameter;

interface ParamResolverInterface
{
    public function resolve(object $attr, ReflectionParameter $parameter): callable;
}
