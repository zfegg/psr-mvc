<?php

declare(strict_types = 1);

namespace Zfegg\PsrMvc\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
class HttpPost extends Route
{
    public function __construct(
        string $path = '',
        array $middlewares = [],
        ?string $name = null,
        ?array $options = [],
    ) {
        parent::__construct($path, $middlewares, $name, $options, ['POST']);
    }
}
