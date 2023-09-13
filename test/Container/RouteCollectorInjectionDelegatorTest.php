<?php

declare(strict_types = 1);

namespace ZfeggTest\PsrMvc\Container;

use ZfeggTest\PsrMvc\AbstractTestCase;

class RouteCollectorInjectionDelegatorTest extends AbstractTestCase
{

    public function testCall(): void
    {
        $this->withCookie('cookie', 'test');
        $this->post('/api/mvc-example/post?query=123', ['body' => 456])->assertNoContent();
        $this->call('HEAD', '/api/mvc-example/head')->assertNoContent();
        $this->get('/api/mvc-example/get-list')->assertNoContent();
        $this->get('/api/mvc-example/get/123')->assertJson([123]);
        $this->put('/api/mvc-example/put', ['foo' => 123, 'bar' => 456])->assertNoContent();
        $this->patch('/api/mvc-example/patch', ['foo' => 123, 'bar' => 456])->assertNoContent();
        $this->delete('/api/mvc-example/delete')->assertNoContent();
    }
}
