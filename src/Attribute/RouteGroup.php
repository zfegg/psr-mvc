<?php

declare(strict_types = 1);

namespace Zfegg\PsrMvc\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class RouteGroup
{
    public function __construct(
        public string $name
    ) {
    }
}
