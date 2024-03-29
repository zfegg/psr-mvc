<?php

declare(strict_types = 1);

namespace ZfeggTest\PsrMvc\Response;

use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\JsonResponse;
use Mezzio\Application;
use Zfegg\PsrMvc\ControllerHandler;
use ZfeggTest\PsrMvc\AbstractTestCase;
use ZfeggTest\PsrMvc\Example\MvcExampleController;

class DefaultResponseTest extends AbstractTestCase
{
    public function testHandler(): void
    {
        $app = $this->container->get(Application::class);
        $app->get('/example[/{action}]', ControllerHandler::class)
            ->setOptions([
                'controller' => MvcExampleController::class,
            ]);

        $response = $this->get('/example/default-html-response', [])
            ->assertSuccessful()->baseResponse;
        $this->assertInstanceOf(HtmlResponse::class, $response);

        $response = $this->get('/example/default-json-response', [])
            ->assertSuccessful()->baseResponse;
        $this->assertInstanceOf(JsonResponse::class, $response);
    }
}
