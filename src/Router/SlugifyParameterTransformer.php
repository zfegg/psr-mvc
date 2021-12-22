<?php

namespace Zfegg\CallableHandlerDecorator\Router;

use Zfegg\CallableHandlerDecorator\Utils\Word;

class SlugifyParameterTransformer implements ParameterTransformer
{

    public function __construct(
        private string $replacement = '-',
    ) {
    }

    public function transform(string $className, string $methodName): array
    {
        $names = explode('\\', $className);
        $className = end($names);
        if (str_ends_with($className, 'Controller')) {
            $className = substr($className, 0, -10);
        }

        return [
            '[controller]' => Word::tableize($className, $this->replacement),
            '[action]' => Word::tableize($methodName, $this->replacement),
        ];
    }
}