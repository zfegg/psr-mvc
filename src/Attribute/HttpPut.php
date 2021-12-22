<?php


namespace Zfegg\CallableHandlerDecorator\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
class HttpPut extends Route
{
    public function __construct(
        string $path = '',
        array $middlewares = [],
        ?string $name = null,
        ?array $options = [],
    )
    {
        parent::__construct($path, $middlewares, $name, $options, ['PUT']);
    }
}
