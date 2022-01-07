<?php

namespace ZfeggTest\PsrMvc\Routing;

use Zfegg\PsrMvc\Routing\Group;
use PHPUnit\Framework\TestCase;

class GroupTest extends TestCase
{

    public function testGets()
    {
        $group = (new Group('/api', ['foo'], 'test.'))->group('/v1', ['bar'], 'v1.');

        $this->assertEquals('test.v1.', $group->getName());
        $this->assertEquals('/api/v1', $group->getPrefix());
        $this->assertEquals(['foo', 'bar'], $group->getMiddlewares());

        $group->get('/test', 'test', 'test');
        $result = $group->getRoutes();
        $this->assertEquals([
            'test.v1.test' => [
                'path' => '/api/v1/test',
                'middleware' => ['foo', 'bar', 'test',],
                'allowed_methods' => ['GET'],
                'name' => 'test.v1.test',
                'options' => [],
            ],
        ], $result);
    }

    public function routeData(): array
    {
        return [
            'get' => ['get', ['GET']],
            'post' => ['post', ['POST']],
            'patch' => ['patch', ['PATCH']],
            'put' => ['put', ['PUT']],
            'delete' => ['delete', ['DELETE']],
            'route' => ['route', null],
            'any' => ['any', null],
        ];
    }

    /**
     * @dataProvider routeData
     */
    public function testGroup(string $methodName, ?array $methods): void
    {
        $group = new Group('/api');

        $group->$methodName('/test', 'test');
        $result = $group->getRoutes();

        $this->assertEquals(
            [
                [
                    'path' => '/api/test',
                    'middleware' => ['test',],
                    'allowed_methods' => $methods,
                    'name' => null,
                    'options' => [],
                ],
            ],
            $result
        );
    }
}
