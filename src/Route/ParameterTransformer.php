<?php

declare(strict_types = 1);

namespace Zfegg\PsrMvc\Route;

interface ParameterTransformer
{
    public function transform(string $className, string $methodName): array;
}
