<?php

declare(strict_types = 1);

namespace ZfeggTest\PsrMvc;

use Mezzio\Application;
use Zfegg\PsrMvc\ControllerHandler;
use ZfeggTest\PsrMvc\Example\MvcExampleController;

class ControllerHandlerTest extends AbstractTestCase
{

    public function testHandler(): void
    {
        $app = $this->container->get(Application::class);
        $app->post('/example[/{action}]', ControllerHandler::class)
            ->setOptions([
                'controller' => MvcExampleController::class,
                'action' => 'home',
            ]);

        $this->post('/example?page_size=123', ['body' => 456])
            ->assertNoContent();

        $this->post('/example/foo-bar', [])
            ->assertNoContent();


        $this->post('/example/not-found', [])
            ->assertNotFound();
    }
}
