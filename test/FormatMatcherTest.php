<?php

declare(strict_types = 1);

namespace ZfeggTest\PsrMvc;

use Laminas\Diactoros\ServerRequestFactory;
use Zfegg\PsrMvc\FormatMatcher;
use PHPUnit\Framework\TestCase;

class FormatMatcherTest extends TestCase
{

    public function testGetBestFormat(): void
    {
        $matcher = new FormatMatcher();

        $req = (new ServerRequestFactory())->createServerRequest('GET', '/')
            ->withAttribute('format', 'json');
        [$format, $contentType] = $matcher->getBestFormat($req);

        $this->assertEquals('json', $format);
        $this->assertEquals('application/json', $contentType);
    }
}
