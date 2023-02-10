<?php

declare(strict_types = 1);

namespace ZfeggTest\PsrMvc\Http;

use Zfegg\PsrMvc\Http\IteratorStream;
use PHPUnit\Framework\TestCase;

class IteratorStreamTest extends TestCase
{

    private IteratorStream $stream;
    private string $content = '';

    protected function setUp(): void
    {
        $data = (function () {
            for ($i = 1; $i < 5; $i++) {
                yield json_encode(['id' => $i, 'a' => ['a1' => 1, 'a2' => 4], 'b' => 2]) . "\n";
            }
        })();

        $this->stream = new IteratorStream($data);

        $this->content = '{"id":1,"a":{"a1":1,"a2":4},"b":2}
{"id":2,"a":{"a1":1,"a2":4},"b":2}
{"id":3,"a":{"a1":1,"a2":4},"b":2}
{"id":4,"a":{"a1":1,"a2":4},"b":2}
';
    }

    public function testGetContents(): void
    {
        $body = $this->stream->getContents();

        $res = $this->content;
        $this->assertEquals($res, $body);
    }

    public function testWhileLoop(): void
    {
        $lines = explode("\n", $this->content);
        $i = 0;
        while (! $this->stream->eof()) {
            $pos = $this->stream->tell();
            $buf = $this->stream->read(0);
            $this->assertEquals($lines[$pos] . "\n", $buf);
            $i++;
        }
    }

    public function testMethods(): void
    {
        $this->assertFalse($this->stream->write(""));
        $this->assertTrue($this->stream->isSeekable());
        $this->assertTrue($this->stream->isReadable());
        $this->assertFalse($this->stream->isWritable());
        $this->assertNull($this->stream->getSize());
        $this->assertEquals(0, $this->stream->tell());
        $this->assertEquals([], $this->stream->getMetadata());

        $this->stream->close();
    }

    public function testSeek(): void
    {
        $stream = new IteratorStream(new \ArrayIterator([
            "a",
            "b",
            "c",
        ]));

        $this->assertTrue($stream->seek(1));
        $rs = $stream->read(0);

        $this->assertEquals('b', $rs);
    }

    public function testDetach(): void
    {
        $this->assertIsIterable($this->stream->detach());
    }

    public function testToString(): void
    {
        $this->assertEquals($this->content, (string)$this->stream);
    }
}
