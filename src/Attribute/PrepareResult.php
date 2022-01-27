<?php

declare(strict_types = 1);

namespace Zfegg\PsrMvc\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class PrepareResult
{
    public function __construct(
        public string $name,
        public array $options = []
    ) {
    }
}
