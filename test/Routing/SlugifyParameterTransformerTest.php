<?php

declare(strict_types = 1);

namespace ZfeggTest\PsrMvc\Routing;

use Zfegg\PsrMvc\Routing\SlugifyParameterConverter;
use PHPUnit\Framework\TestCase;
use ZfeggTest\PsrMvc\Example\MvcExampleController;

class SlugifyParameterTransformerTest extends TestCase
{

    public function testTransform(): void
    {
        $transformer = new SlugifyParameterConverter();
        $result = $transformer->convertClassNameToPath(MvcExampleController::class);
        $this->assertEquals('mvc-example', $result);

        $result = $transformer->convertMethodToPath('getList');
        $this->assertEquals('get-list', $result);
    }
}
