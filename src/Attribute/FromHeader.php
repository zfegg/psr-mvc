<?php


namespace Zfegg\PsrMvc\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_PARAMETER)]
class FromHeader implements InjectFrom
{
    public ?string $name;

    public function __construct(?string $name = null)
    {
        $this->name = $name;
    }
}