<?php declare(strict_types = 1);

namespace ZfeggTest\CallableHandlerDecorator;

use Laminas\Diactoros\Response\JsonResponse;
use Laminas\Diactoros\ServerRequest;
use Laminas\ServiceManager\ServiceManager;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Zfegg\CallableHandlerDecorator\Exception\InvalidArgumentException;
use Zfegg\CallableHandlerDecorator\ReflectionFactory;

class ReflectionFactoryTest extends TestCase
{

    /**
     * @var string
     */
    private $handler = Foo::class . '@test';

    /**
     * @var ServiceManager
     */
    private $container;

    protected function setUp(): void
    {
        $this->container = new ServiceManager(['invokables' => [Foo::class, Bar::class]]);
    }

    public function testExists(): void
    {
        $factory = new ReflectionFactory(new ServiceManager());
        $this->assertFalse($factory->exists($this->handler));

        $factory = new ReflectionFactory($this->container);
        $this->assertTrue($factory->exists($this->handler));
    }


    public function createCallableTypes(): array
    {
        return [
            'anonymous_function' => [
                function (
                    ServerRequestInterface $request,
                    Bar $bar,
                    string $name,
                    array $data,
                    int $id = 123
                ) {
                    return new JsonResponse(['name' => $name, 'data' => $data, 'id' => $id]);
                }
            ],
            'class_invoke' => [
                new Bar()
            ],
            'string' => [
                $this->handler
            ],
        ];
    }

    /**
     * Test create
     * @dataProvider createCallableTypes()
     * @param callable|string $callable
     */
    public function testCreate($callable): void
    {
        $factory = new ReflectionFactory($this->container);
        $handler = $factory->create($callable);
        $request = new ServerRequest();
        $request = $request->withAttribute('name', 'test');
        $request = $request->withAttribute('data', [1,2,3]);

        $response = $handler->handle($request);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $payload = $response->getPayload();

        $this->assertEquals(['name' => 'test', 'data' => [1,2,3], 'id' => 123], $payload);
    }

    public function testUnresolvedParam(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $factory = new ReflectionFactory($this->container);
        $handler = $factory->create($this->handler);
        $request = new ServerRequest();
        $handler->handle($request);
    }
}
