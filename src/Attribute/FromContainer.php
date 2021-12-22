<?php


namespace Zfegg\PsrMvc\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_PARAMETER)]
class FromContainer implements InjectFrom
{
    public string $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }
}