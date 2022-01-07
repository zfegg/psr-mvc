<?php

declare(strict_types = 1);

namespace Zfegg\PsrMvc\Routing;

use Zfegg\PsrMvc\Utils\Word;

class SlugifyParameterConverter implements ParameterConverterInterface
{

    public function __construct(
        private string $pathReplacement = '-',
        private string $paramReplacement = '_',
    ) {
    }

    public function convertClassNameToPath(string $className): string
    {
        $names = explode('\\', $className);
        $className = end($names);
        if (str_ends_with($className, 'Controller')) {
            $className = substr($className, 0, -10);
        }

        return Word::tableize($className, $this->pathReplacement);
    }

    public function convertMethodToPath(string $methodName): string
    {
        return Word::tableize($methodName, $this->pathReplacement);
    }

    public function convertActionToMethod(string $action): string
    {
        return Word::camelize($action);
    }

    public function convertParamToRequestParam(string $paramName): string
    {
        return Word::tableize($paramName, $this->paramReplacement);
    }
}
