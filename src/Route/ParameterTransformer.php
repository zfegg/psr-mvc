<?php

namespace Zfegg\PsrMvc\Route;

interface ParameterTransformer
{
    public function transform(string $className, string $methodName): array;
}