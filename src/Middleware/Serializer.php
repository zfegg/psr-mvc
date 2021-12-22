<?php

declare(strict_types = 1);

namespace Zfegg\PsrMvc\Middleware;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Zfegg\PsrMvc\FormatMatcher;

class Serializer implements MiddlewareInterface
{

    public function __construct(
        private FormatMatcher $matcher,
        private SerializerInterface $serializer,
        private ResponseFactoryInterface $responseFactory,
        private array $context = [],
    ) {
    }

    public function process(ServerRequestInterface $request, callable $next): ResponseInterface
    {
        [$format, $mimeType] = $this->matcher->getBestFormat($request) ?? [null, null];

        if (! $format) {
            return $this->responseFactory->createResponse(406);
        }

        $result = $next();

        if ($result instanceof ResponseInterface) {
            return $result;
        }

        if ($result === null) {
            return $this->responseFactory->createResponse(204);
        }

        $response = $this->responseFactory->createResponse();
        $response = $response->withHeader('Content-Type', $mimeType);
        $response->getBody()->write(
            $this->serializer->serialize(
                $result,
                $format,
                $this->context
            )
        );
        return $response;
    }
}
