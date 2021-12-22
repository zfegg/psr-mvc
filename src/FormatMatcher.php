<?php

declare(strict_types = 1);

namespace Zfegg\PsrMvc;

use Negotiation\Negotiator;
use Psr\Http\Message\ServerRequestInterface;

class FormatMatcher
{

    public const MIME_TYPES = [
        'json' => ['application/json'],
        'jsonld' => ['application/ld+json'],
        'jsonhal' => ['application/hal+json'],
        'jsonapi' => ['application/vnd.api+json'],
        'jsonproblem' => ['application/problem+json'],
        'csv' => ['text/csv'],
        'doc' => ['application/msword'],
        'docx' => ['application/vnd.openxmlformats-officedocument.wordprocessingml.document'],
        'js' => ['application/javascript'],
        'html' => ['text/html', 'application/xhtml+xml'],
        'txt' => ['text/plain'],
        'xml' => ['text/xml', 'application/xml', 'application/x-xml'],
        'xls' => ['application/vnd.ms-excel'],
        'xlsx' => ['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'],
        'yaml' => ['text/yaml'],
        'yml' => ['text/yaml'],
    ];

    /**
     * @var array<string, string[]>
     */
    private array $formats;

    private Negotiator $negotiator;

    /**
     * @var array<string, string>
     */
    private array $mimeTypes = [];

    /**
     * @param array<string, string[]|string> $formats
     */
    public function __construct(?Negotiator $negotiator = null, array $formats = self::MIME_TYPES)
    {
        $this->formats = $this->normalizeFormats($formats);
        $this->negotiator = $negotiator ?? new Negotiator();
    }

    private function normalizeFormats(array $formats): array
    {
        $normalizedFormats = [];
        foreach ($formats as $format => $mimeTypes) {
            if (is_int($format)) {
                $format = $mimeTypes;
                $mimeTypes = self::fromExtension($format);
            }
            $mimeTypes = (array) $mimeTypes;

            $normalizedFormats[$format] = $mimeTypes;
            foreach ($mimeTypes as $mimeType) {
                $this->mimeTypes[$mimeType] = $format;
            }
        }

        return $normalizedFormats;
    }

    public function getBestFormat(ServerRequestInterface $request): ?array
    {
        $format = $request->getAttribute('format') ?:
            $this->detectFromExtension($request) ?:
            $this->detectFromHeader($request)
            ;

        if ($format && isset($this->formats[$format])) {
            return [$format, current($this->formats[$format])];
        }

        return null;
    }

    /**
     * Returns the format using the file extension.
     */
    private function detectFromExtension(ServerRequestInterface $request): ?string
    {
        $extension = strtolower(pathinfo($request->getUri()->getPath(), PATHINFO_EXTENSION));

        return isset($this->formats[$extension]) ? $extension : null;
    }

    /**
     * Returns the format using the Accept header.
     */
    private function detectFromHeader(ServerRequestInterface $request): ?string
    {
        $mimeTypes = array_keys($this->mimeTypes);

        /** @var \Negotiation\Accept $accept */
        $accept = $this->negotiator->getBest(
            $request->getHeaderLine('Accept') ?: '*/*',
            $mimeTypes
        );

        if (! $accept) {
            return null;
        }

        $mimeType = $accept->getType();

        return $this->mimeTypes[$mimeType];
    }

    public static function fromExtension(string $extension): ?array
    {
        $extension = strtolower($extension);

        return self::MIME_TYPES[$extension] ?? null;
    }
}
