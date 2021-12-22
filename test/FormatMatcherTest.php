<?php

namespace ZfeggTest\PsrMvc;

use Laminas\Diactoros\ServerRequestFactory;
use Psr\Http\Message\ServerRequestInterface;
use Zfegg\PsrMvc\FormatMatcher;
use PHPUnit\Framework\TestCase;

class FormatMatcherTest extends TestCase
{

    public function testGetBestFormat()
    {
        $matcher = new FormatMatcher();

        $req = (new ServerRequestFactory())->createServerRequest('GET', '/')
            ->withAttribute('format', 'json');
        [$format, $contentType] = $matcher->getBestFormat($req);

        $this->assertEquals('json', $format);
        $this->assertEquals('application/json', $contentType);
    }
}
