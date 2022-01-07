<?php

declare(strict_types = 1);

namespace Zfegg\PsrMvc\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class PrepareResponse
{
    public function __construct(
        public string $name,
        public array $options = []
    ) {
    }
}
