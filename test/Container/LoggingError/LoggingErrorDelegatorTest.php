<?php

declare(strict_types = 1);

namespace ZfeggTest\PsrMvc\Container\LoggingError;

use Laminas\Stratigility\Middleware\ErrorHandler;
use Psr\Container\ContainerInterface;
use Psr\Log\NullLogger;
use Zfegg\PsrMvc\Container\LoggingError\LoggingErrorDelegator;
use PHPUnit\Framework\TestCase;
use Zfegg\PsrMvc\ErrorHandler\Listener\LoggingErrorListener;

class LoggingErrorDelegatorTest extends TestCase
{

    public function testInvoke(): void
    {
        $listener = new LoggingErrorListener(new NullLogger());
        $container = $this->createMock(ContainerInterface::class);
        $container->method('has')->willReturn(false);
        $container->method('get')->willReturn($listener);

        $factory = new LoggingErrorDelegator();

        $errorHandler = $this->createMock(ErrorHandler::class);
        $errorHandler->expects($this->once())
            ->method('attachListener')
            ->with($listener);

        $result = $factory(
            $container,
            ErrorHandler::class,
            function () use ($errorHandler) {
                return $errorHandler;
            }
        );

        $this->assertInstanceOf(ErrorHandler::class, $result);
    }
}
