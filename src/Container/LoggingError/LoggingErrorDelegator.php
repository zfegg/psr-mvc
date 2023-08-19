<?php

declare(strict_types = 1);

namespace Zfegg\PsrMvc\Container\LoggingError;

use Laminas\Stratigility\Middleware\ErrorHandler;
use Psr\Container\ContainerInterface;
use Zfegg\PsrMvc\ErrorHandler\Listener\LoggingErrorListener;

class LoggingErrorDelegator
{
    public function __invoke(
        ContainerInterface $container,
        string $serviceName,
        callable $callback
    ): ErrorHandler {
        $listener = $container->get(LoggingErrorListener::class);

        $errorHandler = $callback();
        $errorHandler->attachListener($listener);
        return $errorHandler;
    }
}
