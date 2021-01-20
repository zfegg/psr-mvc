<?php


namespace ZfeggTest\CallableHandlerDecorator;


use Laminas\Diactoros\Response\JsonResponse;
use Laminas\Diactoros\Response\TextResponse;
use Psr\Http\Message\ServerRequestInterface;

class Bar
{
    public function __invoke(
        ServerRequestInterface $request,
        string $name,
        array $data,
        int $id = 123
    )
    {
        return new JsonResponse(['name' => $name, 'data' => $data, 'id' => $id]);
    }
}