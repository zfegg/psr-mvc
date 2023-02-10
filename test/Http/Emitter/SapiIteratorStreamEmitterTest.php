<?php

declare(strict_types = 1);

namespace ZfeggTest\PsrMvc\Http\Emitter;

use Laminas\Diactoros\Response;
use Zfegg\PsrMvc\Http\Emitter\SapiIteratorStreamEmitter;
use PHPUnit\Framework\TestCase;
use Zfegg\PsrMvc\Http\IteratorStream;
use function ob_start;

class SapiIteratorStreamEmitterTest extends TestCase
{
    private SapiIteratorStreamEmitter $emitter;
    private IteratorStream $stream;

    protected function setUp(): void
    {
        $this->emitter = new SapiIteratorStreamEmitter();

        $data = (function () {
            for ($i = 1; $i < 5; $i++) {
                yield json_encode(['id' => $i, 'a' => ['a1' => 1, 'a2' => 4], 'b' => 2]) . "\n";
            }
        })();

        $this->stream = new IteratorStream($data);
    }

    public function testUnsupportResponse(): void
    {
        $res = $this->emitter->emit(new Response());
        $this->assertFalse($res);
    }

    public function testEmit(): void
    {
        $response = (new Response())
            ->withStatus(200)
            ->withAddedHeader('Content-Type', 'text/plain');
        $response = $response->withBody($this->stream);

        $this->expectOutputString('{"id":1,"a":{"a1":1,"a2":4},"b":2}
{"id":2,"a":{"a1":1,"a2":4},"b":2}
{"id":3,"a":{"a1":1,"a2":4},"b":2}
{"id":4,"a":{"a1":1,"a2":4},"b":2}
');
        ob_start();
        $this->emitter->emit($response);
    }
}
