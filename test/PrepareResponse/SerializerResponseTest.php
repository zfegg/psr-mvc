<?php

declare(strict_types = 1);

namespace ZfeggTest\PsrMvc\PrepareResponse;

use Mezzio\Application;
use Zfegg\PsrMvc\ControllerHandler;
use ZfeggTest\PsrMvc\AbstractTestCase;
use ZfeggTest\PsrMvc\Example\MvcExampleController;

class SerializerResponseTest extends AbstractTestCase
{
    public function testHandler(): void
    {
        $app = $this->container->get(Application::class);
        $app->get('/example[/{action}]', ControllerHandler::class)
            ->setOptions([
                'controller' => MvcExampleController::class,
            ]);

        $this->get('/example/serialize-result', [])
            ->assertSuccessful()
            ->assertJson(['test']);


        $this->get('/example/serializer-response-assert-returned', ['Accept' => 'invalid/mime-type'])
            ->assertNoContent();


        $this->get('/example/serializer-response-assert-void', ['Accept' => 'invalid/mime-type'])
            ->assertNoContent();
    }
}
