<?php

declare(strict_types = 1);

namespace ZfeggTest\PsrMvc\Example;

use Laminas\Diactoros\Response\EmptyResponse;
use PHPUnit\Framework\Assert;
use Psr\Http\Message\ResponseInterface;
use Zfegg\PsrMvc\Attribute\FromAttribute;
use Zfegg\PsrMvc\Attribute\FromBody;
use Zfegg\PsrMvc\Attribute\FromContainer;
use Zfegg\PsrMvc\Attribute\FromCookie;
use Zfegg\PsrMvc\Attribute\FromHeader;
use Zfegg\PsrMvc\Attribute\FromQuery;
use Zfegg\PsrMvc\Attribute\FromServer;
use Zfegg\PsrMvc\Attribute\HttpDelete;
use Zfegg\PsrMvc\Attribute\HttpGet;
use Zfegg\PsrMvc\Attribute\HttpHead;
use Zfegg\PsrMvc\Attribute\HttpPatch;
use Zfegg\PsrMvc\Attribute\HttpPost;
use Zfegg\PsrMvc\Attribute\HttpPut;
use Zfegg\PsrMvc\Attribute\PrepareResult;
use Zfegg\PsrMvc\Attribute\Route;
use Zfegg\PsrMvc\Attribute\RouteGroup;
use Zfegg\PsrMvc\Middleware\ContentTypeMiddleware;
use Zfegg\PsrMvc\Preparer\DefaultPreparer;
use Zfegg\PsrMvc\Preparer\SerializationPreparer;
use Zfegg\PsrMvc\Routing\ParameterConverterInterface;

#[RouteGroup('test')]
#[Route('/[controller]/[action]', name: '[controller].[action]')]
class MvcExampleController
{

    public function home(
        #[FromQuery]
        int $pageSize,
        #[FromBody]
        int $body,
        #[FromContainer]
        ParameterConverterInterface $converter
    ): void {
        Assert::assertEquals(123, $pageSize);
        Assert::assertEquals(456, $body);
    }

    public function fooBar(string $action): void
    {
        Assert::assertEquals('foo-bar', $action);
    }

    #[HttpGet(middlewares: [ContentTypeMiddleware::class])]
    public function middleware(): void
    {
    }

    #[PrepareResult(DefaultPreparer::class)]
    public function defaultHtmlResponse(): string
    {
        return 'test';
    }

    #[PrepareResult(DefaultPreparer::class)]
    public function defaultJsonResponse(): array
    {
        return ['test'];
    }

    #[PrepareResult(SerializationPreparer::class, ['status' => 201, 'headers' => ['X-Foo' => 'foo']])]
    public function serializeResult(): array
    {
        return ['test'];
    }

    #[HttpPost]
    public function post(
        #[FromQuery]
        int $query,
        #[FromBody]
        int $body,
        #[FromContainer('foo')]
        string $container,
        #[FromCookie]
        string $cookie,
        #[FromHeader]
        string $host,
        #[FromServer('REMOTE_ADDR')]
        string $ip,
    ): void {
        return ;
    }

    #[HttpHead]
    public function head(): ResponseInterface
    {
        return new EmptyResponse();
    }

    #[HttpGet]
    public function getList(): void
    {
    }

    #[HttpGet('{id}', name: 'get')]
    public function get(
        #[FromAttribute]
        int $id,
    ): array {
        return [$id];
    }

    #[HttpPut]
    public function put(
        #[FromBody]
        Post $post
    ): void {
    }

    #[HttpPatch]
    public function patch(
        #[FromBody(root: true)]
        array $post
    ): void {
        return ;
    }

    #[HttpDelete]
    public function delete(): void
    {
    }
}
