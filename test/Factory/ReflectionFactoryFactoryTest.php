<?php

namespace ZfeggTest\CallableHandlerDecorator\Factory;

use Laminas\Diactoros\Response\EmptyResponse;
use Laminas\Diactoros\Response\TextResponse;
use Laminas\Diactoros\ServerRequest;
use Laminas\ServiceManager\ServiceManager;
use Zfegg\CallableHandlerDecorator\Factory\ReflectionFactoryFactory;
use PHPUnit\Framework\TestCase;

class ReflectionFactoryFactoryTest extends TestCase
{

    public function testInvoke()
    {
        $refFactory = (new ReflectionFactoryFactory())(new ServiceManager());

        $handler = $refFactory->create(function ($nameCase) {
            return new TextResponse($nameCase);
        });

        $req = new ServerRequest();
        $req = $req->withAttribute('name_case', 'test');
        $response = $handler->handle($req);

        $this->assertEquals('test', (string) $response->getBody());
    }
}
