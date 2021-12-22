<?php

namespace Zfegg\PsrMvc\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class Middleware
{
    public function __construct(
        public string $name,
        public ?array $options = null
    ) {
    }
}