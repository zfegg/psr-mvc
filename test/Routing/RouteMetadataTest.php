<?php

declare(strict_types = 1);

namespace ZfeggTest\PsrMvc\Routing;

use Laminas\Di\ConfigInterface;
use Zfegg\PsrMvc\Attribute\Route;
use Zfegg\PsrMvc\Routing\RouteMetadata;
use ZfeggTest\PsrMvc\Example\Example2Controller;
use ZfeggTest\PsrMvc\Example\MvcExampleController;
use ZfeggTest\PsrMvc\AbstractTestCase;

class RouteMetadataTest extends AbstractTestCase
{

    public function testGetRoutes(): void
    {
        $excludes = [__DIR__ . '/../Example/Post.php'];
        $routeMetadata = $this->container->get(RouteMetadata::class);
        $routeMetadata->addPaths(['not-exists-path']);
        $routeMetadata->addGroup('foo', ['prefix' => '/foo']);
        $routeMetadata->addExcludePaths($excludes);
        $routeMetadata->setFileExtension('Controller.php');

        $this->assertEquals($excludes, $routeMetadata->getExcludePaths());
        $this->assertCount(2, $routeMetadata->getPaths());
        $this->assertEquals('Controller.php', $routeMetadata->getFileExtension());
        $classes = $routeMetadata->getAllClassNames();

        $this->assertEquals(
            [Example2Controller::class, MvcExampleController::class,],
            $classes
        );

        $metas = $routeMetadata->getRoutes();
        $this->assertCount(9, $metas);

        foreach ($metas as [$route, [$className, $method]]) {
            $this->assertInstanceOf(Route::class, $route);
            $this->assertStringStartsWith('/api/mvc-example/', $route->path);
            $this->assertStringStartsWith('api.test.', $route->name);
            $this->assertTrue(in_array($className, [Example2Controller::class, MvcExampleController::class,]));
        }
    }

    public function testGetRoutesFromCache(): void
    {
        $file = tmpfile();
        $path = stream_get_meta_data($file)['uri'];
        $config = $this->container->get(ConfigInterface::class);
        $config->setParameters(
            RouteMetadata::class,
            $config->getParameters(RouteMetadata::class) + ['cacheFile' => $path]
        );
        $routeMetadata = $this->container->get(RouteMetadata::class);
        $metas = $routeMetadata->getRoutes();
        $this->assertCount(9, $metas);

        // From cache
        $metas = $routeMetadata->getRoutes();
        $this->assertCount(9, $metas);
    }
}
