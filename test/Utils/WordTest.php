<?php

declare(strict_types = 1);

namespace ZfeggTest\PsrMvc\Utils;

use Zfegg\PsrMvc\Utils\Word;
use PHPUnit\Framework\TestCase;

class WordTest extends TestCase
{

    public function testCamelize(): void
    {
        $this->assertEquals('fooBarBaz', Word::camelize('foo-bar-baz'));
        $this->assertEquals('fooBarBaz', Word::camelize('foo_bar_baz'));
        $this->assertEquals('fooBarBaz', Word::camelize('foo bar baz'));
    }

    public function testClassify(): void
    {
        $this->assertEquals('FooBarBaz', Word::classify('foo-bar-baz'));
        $this->assertEquals('FooBarBaz', Word::classify('foo_bar_baz'));
        $this->assertEquals('FooBarBaz', Word::classify('foo bar baz'));
    }

    public function testTableize(): void
    {
        $this->assertEquals('foo-bar-baz', Word::tableize('FooBarBaz', '-'));
    }
}
