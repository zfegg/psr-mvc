<?php

declare(strict_types = 1);

namespace Zfegg\PsrMvc\ErrorHandler\Listener;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;

final class LoggingErrorListener
{

    public function __construct(
        private LoggerInterface $logger,
        private string $message = '%d "%s %s": <<<%s<<<',
        private bool $onlyServerError = true
    ) {
    }

    public function __invoke(
        \Throwable $error,
        ServerRequestInterface $request,
        ResponseInterface $response
    ): void {

        if ($this->onlyServerError && $response->getStatusCode() < 500) {
            return ;
        }

        $this->logger->error(
            sprintf(
                $this->message,
                $response->getStatusCode(),
                $request->getMethod(),
                (string)$request->getUri(),
                $error->getMessage()
            ),
            [
                'error'  => $error,
            ]
        );
    }
}
