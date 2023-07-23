<?php

declare(strict_types = 1);

namespace Zfegg\PsrMvc\ErrorHandler;

use Laminas\Diactoros\Response\JsonResponse;
use Laminas\Stratigility\Utils;
use Mezzio\Middleware\ErrorResponseGenerator as MezzioErrorResponseGenerator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;
use Zfegg\PsrMvc\Exception\HttpExceptionInterface;

class ErrorResponseGenerator
{

    public function __construct(
        private MezzioErrorResponseGenerator $errorHandler,
        private bool $debug = false,
    ) {
    }

    private function isAjax(ServerRequestInterface $request): bool
    {
        return str_starts_with($request->getAttribute('format', ''), 'json') ||
            strtolower($request->getHeaderLine('x-requested-with')) == 'xmlhttprequest';
    }

    public function __invoke(
        Throwable $e,
        ServerRequestInterface $request,
        ResponseInterface $response
    ): ResponseInterface {
        if ($this->isAjax($request)) {
            if ($e instanceof HttpExceptionInterface) {
                return new JsonResponse(
                    $this->convertExceptionToArray($e),
                    $e->getStatusCode(),
                    $e->getHeaders()
                );
            }
            $status = Utils::getStatusCode($e, $response);
            return new JsonResponse(
                $this->convertExceptionToArray($e),
                $status,
            );
        }
        $response = ($this->errorHandler)($e, $request, $response);
        if ($e instanceof HttpExceptionInterface) {
            if ($response->getStatusCode() != $e->getStatusCode()) {
                $response = $response->withStatus($e->getStatusCode());
            }
            foreach ($e->getHeaders() as $name => $value) {
                $response = $response->withHeader($name, $value);
            }
        }

        return $response;
    }

    private function convertExceptionToArray(Throwable $e): array
    {
        return $this->debug ? [
            'code' => $e->getCode(),
            'message' => $e->getMessage(),
            'exception' => get_class($e),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => array_map(
                function ($item): array {
                    unset($item['args']);
                    return $item;
                },
                $e->getTrace()
            ),
        ] : [
            'code' => $e->getCode(),
            'message' => $e instanceof HttpExceptionInterface ? $e->getMessage() : 'Server Error',
        ];
    }
}
