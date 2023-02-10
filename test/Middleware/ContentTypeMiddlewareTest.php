<?php

declare(strict_types = 1);

namespace ZfeggTest\PsrMvc\Middleware;

use Mezzio\Application;
use ZfeggTest\PsrMvc\AbstractTestCase;

class ContentTypeMiddlewareTest extends AbstractTestCase
{
    public function testHandler(): void
    {
        $this->container->get(Application::class);
        $this->get('/api/mvc-example/middleware', ['Accept' => 'invalid type'])
            ->assertStatus(406);

        $this->get('/api/mvc-example/middleware', ['Accept' => '*/*'])
            ->assertNoContent();
    }
}
