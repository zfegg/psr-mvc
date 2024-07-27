<?php

declare(strict_types = 1);

namespace ZfeggTest\PsrMvc\Example;

use Zfegg\PsrMvc\Attribute\HttpGet;

class Example2Controller
{

    #[HttpGet("/api/mvc-example/home", name: 'api.test.home')]
    public function home(): void
    {
    }
}
