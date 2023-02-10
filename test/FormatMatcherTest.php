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
        $matcher = new FormatMatcher(formats: ['json', 'csv']);

        $req = (new ServerRequestFactory())->createServerRequest('GET', '/')
            ->withAttribute('format', 'json');
        $format = $matcher->getBestFormat($req);
        $contentType = $matcher->getFormat($format)['mime-type'][0];

        $this->assertEquals('json', $matcher->getDefaultFormat());
        $this->assertEquals('json', $format);
        $this->assertEquals('application/json', $contentType);
    }
}
