<?php

declare(strict_types = 1);

namespace ZfeggTest\PsrMvc\Utils;

use Laminas\Diactoros\Response;
use Zfegg\PsrMvc\Utils\ResponseUtils;
use PHPUnit\Framework\TestCase;

class ResponseUtilsTest extends TestCase
{

    protected function setUp(): void
    {
    }

    public function testIsNotFound(): void
    {
        $response = new Response();
        $response = $response->withStatus(404);
        self::assertTrue(ResponseUtils::isNotFound($response));
    }

    public function testIsRedirect(): void
    {
        $response = new Response\RedirectResponse('/');
        self::assertTrue(ResponseUtils::isRedirect($response));
    }

    public function testIsEmpty(): void
    {
        $response = new Response\EmptyResponse();
        self::assertTrue(ResponseUtils::isEmpty($response));
    }

    public function testIsClientError(): void
    {
        $response = new Response();
        $response = $response->withStatus(400);
        self::assertTrue(ResponseUtils::isClientError($response));

        $response = $response->withStatus(499);
        self::assertTrue(ResponseUtils::isClientError($response));
    }

    public function testIsForbidden(): void
    {
        $response = new Response();
        $response = $response->withStatus(403);
        self::assertTrue(ResponseUtils::isForbidden($response));
    }

    public function testIsSuccessful(): void
    {
        $response = new Response\EmptyResponse();
        self::assertTrue(ResponseUtils::isSuccessful($response));
    }

    public function testIsServerError(): void
    {
        $response = new Response();
        $response = $response->withStatus(503);
        self::assertTrue(ResponseUtils::isServerError($response));
    }

    public function testIsRedirection(): void
    {
        $response = new Response();
        $response = $response->withStatus(317);
        self::assertTrue(ResponseUtils::isRedirection($response));
    }

    public function testIsOk(): void
    {
        $response = new Response();
        self::assertTrue(ResponseUtils::isOk($response));
    }

    public function testToArray(): void
    {
        $data = ['foo'];
        $response = new Response\JsonResponse($data);
        self::assertEquals($data, ResponseUtils::toArray($response));
    }

    public function testIsInformational(): void
    {
        $response = new Response();
        $response = $response->withStatus(100);
        self::assertTrue(ResponseUtils::isInformational($response));
    }
}
