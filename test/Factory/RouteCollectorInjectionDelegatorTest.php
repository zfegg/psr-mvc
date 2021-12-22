<?php

namespace ZfeggTest\PsrMvc\Factory;

use ZfeggTest\PsrMvc\AbstractTestCase;

class RouteCollectorInjectionDelegatorTest extends AbstractTestCase
{

    public function testCall()
    {
        $this->withCookie('cookie', 'test');
        $this->post('/mvc-example/post?query=123', ['body' => 456])->assertNoContent();
        $this->call('HEAD', '/mvc-example/head')->assertNoContent();
        $this->get('/mvc-example/get-list')->assertNoContent();
        $this->get('/mvc-example/get/123')->assertJson([123]);
        $this->put('/mvc-example/put')->assertNoContent();
        $this->patch('/mvc-example/patch')->assertNoContent();
        $this->delete('/mvc-example/delete')->assertNoContent();
    }
}
