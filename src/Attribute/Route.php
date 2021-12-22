<?php
declare(strict_types=1);

namespace Zfegg\PsrMvc\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
class Route
{
    /**
     * Unique name that will be used to identify route for URI generation.
     */
    public ?string $name;

    /**
     * Route path pattern.
     * The exact syntax depends on router you choose.
     * @Required
     * @var string
     */
    public string $path;

    public array $middlewares = [];

    public array $options = [];

    public ?array $methods = [];

    public function __construct(
        string $path,
        array $middlewares = [],
        ?string $name = null,
        array $options = [],
        ?array $methods = null,
    ) {
        $this->path = $path;
        $this->middlewares = $middlewares;
        $this->name = $name;
        $this->options = $options;
        $this->methods = $methods;
    }
}
