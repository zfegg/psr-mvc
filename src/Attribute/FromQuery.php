<?php


namespace Zfegg\CallableHandlerDecorator\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_PARAMETER)]
class FromQuery implements InjectFrom
{
    public ?string $name;

    public function __construct(?string $name = null)
    {
        $this->name = $name;
    }
}