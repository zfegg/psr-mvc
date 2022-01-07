<?php

namespace ZfeggTest\PsrMvc\PrepareResponse;

use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\JsonResponse;
use Mezzio\Application;
use Zfegg\PsrMvc\ControllerHandler;
use Zfegg\PsrMvc\PrepareResponse\SerializerResponse;
use PHPUnit\Framework\TestCase;
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


        $this->get('/example/serialize-result', ['Accept' => 'invalid/mime-type'])
            ->assertSuccessful()
            ->assertJson(['test']);
    }
}
