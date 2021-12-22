<?php

declare(strict_types = 1);

namespace ZfeggTest\PsrMvc\Example;

use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Foo
{
    public function test(
        ServerRequestInterface $request,
        Bar $bar,
        string $name,
        array $data,
        ?string $nullable,
        Baz $bazParam,
        int $id = 123
    ): ResponseInterface {
        return new JsonResponse(['name' => $name, 'data' => $data, 'id' => $id, 'nullable' => null]);
    }
}
