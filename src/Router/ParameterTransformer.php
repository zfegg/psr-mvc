<?php

namespace Zfegg\CallableHandlerDecorator\Router;

interface ParameterTransformer
{
    public function transform(string $className, string $methodName): array;
}