<?php

declare(strict_types = 1);

namespace Zfegg\PsrMvc\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_PARAMETER)]
class FromServer implements InjectFrom
{
    public string $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }
}
