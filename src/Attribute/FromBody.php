<?php

declare(strict_types = 1);

namespace Zfegg\PsrMvc\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_PARAMETER)]
class FromBody implements ParamResolverAttributeInterface
{

    public function __construct(
        public ?string $name = null,
        public bool $root = false,
        public array $serializerContext = []
    ) {
    }
}
