<?php

declare(strict_types = 1);

namespace ZfeggTest\PsrMvc\Route;

use Zfegg\PsrMvc\Route\SlugifyParameterTransformer;
use PHPUnit\Framework\TestCase;
use ZfeggTest\PsrMvc\Example\MvcExampleController;

class SlugifyParameterTransformerTest extends TestCase
{

    public function testTransform(): void
    {
        $transformer = new SlugifyParameterTransformer();
        $result = $transformer->transform(MvcExampleController::class, 'getList');

        $this->assertEquals(
            [
                '[controller]' => 'mvc-example',
                '[action]' => 'get-list',
            ],
            $result
        );
    }
}
