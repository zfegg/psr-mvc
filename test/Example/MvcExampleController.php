<?php

declare(strict_types = 1);

namespace ZfeggTest\PsrMvc\Example;

use Laminas\Diactoros\Response\EmptyResponse;
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
use Zfegg\PsrMvc\Attribute\Middleware;
use Zfegg\PsrMvc\Attribute\Route;
use Zfegg\PsrMvc\Middleware\JsonSerializer;

#[Route('/[controller]/[action]')]
class MvcExampleController
{

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

    #[HttpGet('{id}')]
    #[Middleware(JsonSerializer::class)]
    public function get(
        #[FromAttribute]
        int $id,
    ): array {
        return [$id];
    }

    #[HttpPut]
    public function put(): void
    {
    }

    #[HttpPatch]
    public function patch(): void
    {
    }

    #[HttpDelete]
    public function delete(): void
    {
    }
}
