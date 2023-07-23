<?php

declare(strict_types = 1);

namespace ZfeggTest\PsrMvc\ErrorHandler\Listener;

use Laminas\Diactoros\Response;
use Laminas\Diactoros\ServerRequestFactory;
use Monolog\Handler\TestHandler;
use Monolog\Logger;
use Monolog\Processor\PsrLogMessageProcessor;
use PHPUnit\Framework\TestCase;
use Psr\Log\LogLevel;
use Zfegg\PsrMvc\ErrorHandler\Listener\LoggingErrorListener;

class LoggingErrorListenerTest extends TestCase
{
    public function testLogging(): void
    {
        $testHandler = new TestHandler();
        $logger = new Logger('test', [$testHandler], [new PsrLogMessageProcessor()]);

        $listener = new LoggingErrorListener($logger, '%s "%s %s"');
        $listener(
            new \Exception('test'),
            (new ServerRequestFactory())->createServerRequest('GET', '/'),
            (new Response())->withStatus(500)
        );

        $this->assertTrue($testHandler->hasError('500 "GET /"'));
    }


    public function testIgnoreClientError(): void
    {
        $testHandler = new TestHandler();
        $logger = new Logger('test', [$testHandler], [new PsrLogMessageProcessor()]);

        $listener = new LoggingErrorListener($logger, '%s "%s %s"');
        $listener(
            new \Exception('test'),
            (new ServerRequestFactory())->createServerRequest('GET', '/'),
            (new Response())->withStatus(499)
        );

        $this->assertFalse($testHandler->hasRecords(LogLevel::ERROR));
    }
}
