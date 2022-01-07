<?php

namespace ZfeggTest\PsrMvc\Middleware;

use Mezzio\Application;
use Zfegg\PsrMvc\ControllerHandler;
use ZfeggTest\PsrMvc\AbstractTestCase;
use ZfeggTest\PsrMvc\Example\MvcExampleController;

class ContentTypeMiddlewareTest extends AbstractTestCase
{
    public function testHandler(): void
    {
        $this->container->get(Application::class);
        $this->get('/api/mvc-example/middleware', ['Accept' => 'invalid type'])
            ->assertStatus(406);

        $this->get('/api/mvc-example/middleware')
            ->assertNoContent();
    }
}
