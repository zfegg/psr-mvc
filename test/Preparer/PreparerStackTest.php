<?php

declare(strict_types = 1);

namespace ZfeggTest\PsrMvc\Preparer;

use Laminas\Diactoros\Response\EmptyResponse;
use Laminas\Diactoros\ServerRequest;
use Zfegg\PsrMvc\Exception\InvalidArgumentException;
use Zfegg\PsrMvc\Preparer\CommonPreparer;
use Zfegg\PsrMvc\Preparer\PreparerStack;
use PHPUnit\Framework\TestCase;

class PreparerStackTest extends TestCase
{
    private PreparerStack $preparer;

    protected function setUp(): void
    {
        $this->preparer = new PreparerStack();
        $this->preparer->push(new CommonPreparer());
    }

    public function testPrepare(): void
    {
        $req = new ServerRequest();
        $this->assertInstanceOf(EmptyResponse::class, $this->preparer->prepare($req, null));
    }

    public function testSupportsPreparation(): void
    {
        $req = new ServerRequest();
        $this->assertTrue($this->preparer->supportsPreparation($req, null));
    }

    public function testPrepareError(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $req = new ServerRequest();
        $this->assertInstanceOf(EmptyResponse::class, $this->preparer->prepare($req, []));
    }
}
