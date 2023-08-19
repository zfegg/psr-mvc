<?php

declare(strict_types = 1);

namespace ZfeggTest\PsrMvc\Utils;

use Laminas\Diactoros\ServerRequest;
use Zfegg\PsrMvc\Utils\RequestUtils;
use PHPUnit\Framework\TestCase;

class RequestUtilsTest extends TestCase
{

    public function testIsXmlHttpRequest(): void
    {
        $request = new ServerRequest(headers: ['X-Requested-With' => 'XMLHttpRequest']);
        self::assertTrue(RequestUtils::isXmlHttpRequest($request));
    }
}
