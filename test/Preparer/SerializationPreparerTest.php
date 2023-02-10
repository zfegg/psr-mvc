<?php

declare(strict_types = 1);

namespace ZfeggTest\PsrMvc\Preparer;

use Laminas\Diactoros\ResponseFactory;
use Laminas\Diactoros\ServerRequest;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Serializer;
use Zfegg\PsrMvc\FormatMatcher;
use Zfegg\PsrMvc\Preparer\SerializationPreparer;
use PHPUnit\Framework\TestCase;

class SerializationPreparerTest extends TestCase
{
    private SerializationPreparer $preparer;

    protected function setUp(): void
    {
        $this->preparer = new SerializationPreparer(
            new FormatMatcher(),
            new Serializer(
                encoders: [
                    new JsonEncoder(),
                ]
            ),
            new ResponseFactory(),
        );
    }

    public function testPrepare(): void
    {
        $req = new ServerRequest();
        $response = $this->preparer->prepare($req, []);
        $this->assertEquals('application/json', $response->getHeaderLine('Content-Type'));
        $this->assertEquals('[]', (string)$response->getBody());
    }

    public function testSupportsPreparation(): void
    {
        $req = new ServerRequest();
        $this->assertTrue($this->preparer->supportsPreparation($req, []));
    }
}
