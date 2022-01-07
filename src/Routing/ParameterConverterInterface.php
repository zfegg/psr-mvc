<?php

declare(strict_types = 1);

namespace Zfegg\PsrMvc\Routing;

interface ParameterConverterInterface
{
    public function convertClassNameToPath(string $className): string;
    public function convertMethodToPath(string $methodName): string;
    public function convertActionToMethod(string $action): string;

    /**
     * Replace method param names to request param names.
     * Converts 'pageSize' to 'page_size'.
     */
    public function convertParamToRequestParam(string $paramName): string;
}
