<?php

declare(strict_types = 1);

namespace ZfeggTest\PsrMvc\Route;

use Zfegg\PsrMvc\Attribute\Route;
use Zfegg\PsrMvc\Route\RouteMetadata;
use ZfeggTest\PsrMvc\Example\MvcExampleController;
use ZfeggTest\PsrMvc\AbstractTestCase;

class RouteMetadataTest extends AbstractTestCase
{

    public function testGetRoutes(): void
    {
        $routeMetadata = $this->container->get(RouteMetadata::class);
        $routeMetadata->setFileExtension('Controller.php');
        $classes = $routeMetadata->getAllClassNames();

        $this->assertEquals(
            [MvcExampleController::class],
            $classes
        );

        $metas = $routeMetadata->getRoutes();
        $methods = get_class_methods(MvcExampleController::class);
        $this->assertCount(count($methods), $metas);

        foreach ($metas as [$route, [$className, $method]]) {
            $this->assertInstanceOf(Route::class, $route);
            $this->assertStringStartsWith('/mvc-example/', $route->path);
            $this->assertEquals(MvcExampleController::class, $className);
        }
    }
}
