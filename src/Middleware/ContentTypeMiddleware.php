<?php

declare(strict_types = 1);

namespace Zfegg\PsrMvc\Middleware;

use Exception;
use Negotiation\CharsetNegotiator;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zfegg\PsrMvc\FormatMatcher;

class ContentTypeMiddleware implements MiddlewareInterface
{
    public function __construct(
        private FormatMatcher $matcher,
        private ?ResponseFactoryInterface $responseFactory = null,
        private array $charsets = ['UTF-8']
    ) {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $format = $request->getAttribute('format') ?: $this->matcher->getBestFormat($request);

        if ($format === null) {
            if ($this->responseFactory) {
                return $this->responseFactory->createResponse(406);
            }

            $format = $this->matcher->getDefaultFormat();
        }

        $formatData = $this->matcher->getFormat($format);

        $contentType = $formatData['mime-type'][0];

        $request = $request->withAttribute('format', $format);

        $response = $handler->handle($request);

        if (! $response->hasHeader('Content-Type')) {
            if (! empty($formatData['charset'])) {
                $charset = $this->detectCharset($request) ?: current($this->charsets);
                $contentType .= '; charset=' . $charset;
            }

            $response = $response->withHeader('Content-Type', $contentType);
        }

        return $response;
    }

    /**
     * Returns the charset accepted.
     */
    private function detectCharset(ServerRequestInterface $request): ?string
    {
        $accept = $request->getHeaderLine('Accept-Charset');

        try {
            $best = (new CharsetNegotiator())->getBest($accept, $this->charsets);
            return $best?->getValue();
        } catch (Exception $exception) {
            return null;
        }
    }
}
