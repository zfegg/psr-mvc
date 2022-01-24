<?php

declare(strict_types = 1);

namespace Zfegg\PsrMvc\PrepareResponse;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Zfegg\PsrMvc\FormatMatcher;

class SerializerResponse implements PrepareResponseInterface
{
    use DefaultPrepareTrait;

    public function __construct(
        private FormatMatcher $matcher,
        private SerializerInterface $serializer,
        private ResponseFactoryInterface $responseFactory,
    ) {
    }

    public function prepare(ServerRequestInterface $request, mixed $result, array $options = []): ResponseInterface
    {
        if ($response = $this->defaultPrepare($result)) {
            return $response;
        }

        $format = $request->getAttribute('format')
            ?: $this->matcher->getBestFormat($request)
            ?: $this->matcher->getDefaultFormat();

        $mimeType = $this->matcher->getFormat($format)['mime-type'][0];
        $response = $this->responseFactory->createResponse($options['status'] ?? 200);
        $response = $response->withHeader('Content-Type', $mimeType);

        foreach ($options['headers'] ?? [] as $name => $header) {
            $response = $response->withHeader($name, $header);
        }

        $response->getBody()->write(
            $this->serializer->serialize(
                $result,
                $format,
                $options
            )
        );
        return $response;
    }
}
