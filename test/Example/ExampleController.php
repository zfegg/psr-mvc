<?php


namespace ZfeggTest\CallableHandlerDecorator\Example;


use Zfegg\CallableHandlerDecorator\Attribute\FromAttribute;
use Zfegg\CallableHandlerDecorator\Attribute\FromBody;
use Zfegg\CallableHandlerDecorator\Attribute\FromContainer;
use Zfegg\CallableHandlerDecorator\Attribute\FromCookie;
use Zfegg\CallableHandlerDecorator\Attribute\FromQuery;
use Zfegg\CallableHandlerDecorator\Attribute\HttpDelete;
use Zfegg\CallableHandlerDecorator\Attribute\HttpGet;
use Zfegg\CallableHandlerDecorator\Attribute\HttpPatch;
use Zfegg\CallableHandlerDecorator\Attribute\HttpPost;
use Zfegg\CallableHandlerDecorator\Attribute\HttpPut;
use Zfegg\CallableHandlerDecorator\Attribute\Route;

#[Route('/[controller]/[action]')]
class ExampleController
{

    #[HttpPost]
    public function post(
        #[FromQuery]
        int $query,
        #[FromBody]
        int $body,
        #[FromAttribute]
        int $attr,
        #[FromContainer('foo')]
        string $container,
        #[FromCookie]
        string $cookie
    )
    {
    }

    #[HttpGet]
    public function get()
    {
    }

    #[HttpPut]
    public function put()
    {
    }

    #[HttpPatch]
    public function patch()
    {
    }

    #[HttpDelete]
    public function delete()
    {
    }
}