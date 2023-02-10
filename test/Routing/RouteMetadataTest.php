<?php

declare(strict_types = 1);

namespace ZfeggTest\PsrMvc\Routing;

use Zfegg\PsrMvc\Attribute\Route;
use Zfegg\PsrMvc\Routing\RouteMetadata;
use ZfeggTest\PsrMvc\Example\MvcExampleController;
use ZfeggTest\PsrMvc\AbstractTestCase;

class RouteMetadataTest extends AbstractTestCase
{

    public function testGetRoutes(): void
    {
        $routeMetadata = $this->container->get(RouteMetadata::class);
        $routeMetadata->addGroup('foo', ['prefix' => '/foo']);
        $routeMetadata->addExcludePaths([__DIR__ . '/../Example/Post.php']);
        $routeMetadata->setFileExtension('Controller.php');
        $this->assertCount(1, $routeMetadata->getPaths());
        $this->assertEquals('Controller.php', $routeMetadata->getFileExtension());
        $classes = $routeMetadata->getAllClassNames();

        $this->assertEquals(
            [MvcExampleController::class],
            $classes
        );

        $metas = $routeMetadata->getRoutes();
        $this->assertCount(8, $metas);

        foreach ($metas as [$route, [$className, $method]]) {
            $this->assertInstanceOf(Route::class, $route);
            $this->assertStringStartsWith('/api/mvc-example/', $route->path);
            $this->assertEquals(MvcExampleController::class, $className);
        }
    }
}
