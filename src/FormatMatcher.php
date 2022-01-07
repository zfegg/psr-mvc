<?php

declare(strict_types = 1);

namespace Zfegg\PsrMvc;

use InvalidArgumentException;
use Negotiation\Negotiator;
use Psr\Http\Message\ServerRequestInterface;

class FormatMatcher
{
    /**
     * @var array<string, string[]>
     */
    private array $formats;

    private array $extensions = [];

    private array $mimeTypes = [];
    private Negotiator $negotiator;
    private string $defaultFormat;

    /**
     * @param array<string, string[]|string> $formats
     */
    public function __construct(?array $formats = null)
    {
        $this->formats = self::normalizeFormats($formats);
        $this->negotiator = new Negotiator();
        $this->defaultFormat = key($this->formats);
    }

    public function getFormat(string $format): array
    {
        return $this->formats[$format];
    }

    public function getDefaultFormat(): string
    {
        return $this->defaultFormat;
    }

    /**
     * Return the default formats.
     */
    public static function getDefaultFormats(): array
    {
        return require __DIR__ . '/formats_defaults.php';
    }

    private function normalizeFormats(?array $formats = null): array
    {
        $defaults = static::getDefaultFormats();

        if (empty($formats)) {
            $formats = $defaults;
        }

        $results = [];
        foreach ($formats as $format => $config) {
            if (is_string($config) && isset($defaults[$config])) {
                $format = $config;
                $config = $defaults[$config];
            }

            $config['extension'] = $config['extension'] ?? [$format];

            if (empty($config['mime-type']) || empty($config['extension'])) {
                throw new InvalidArgumentException(
                    sprintf('Invalid format name %s', $config)
                );
            }

            $results[$format] = $config;
            foreach ($config['extension'] as $ext) {
                $this->extensions[$ext] = $format;
            }

            foreach ($config['mime-type'] as $mimeType) {
                $this->mimeTypes[$mimeType] = $format;
            }
        }

        return $results;
    }

    public function getBestFormat(ServerRequestInterface $request): ?string
    {
        return $this->detectFromExtension($request) ?: $this->detectFromHeader($request);
    }

    /**
     * Returns the format using the file extension.
     */
    private function detectFromExtension(ServerRequestInterface $request): ?string
    {
        $extension = strtolower(pathinfo($request->getUri()->getPath(), PATHINFO_EXTENSION));

        return $this->extensions[$extension] ?? null;
    }


    /**
     * Returns the format using the Accept header.
     */
    private function detectFromHeader(ServerRequestInterface $request): ?string
    {
        $mimeTypes = array_keys($this->mimeTypes);

        $accept = $this->negotiator->getBest(
            $request->getHeaderLine('Accept') ?: '*/*',
            $mimeTypes
        );

        if (! $accept) {
            return null;
        }

        return $this->mimeTypes[$accept->getType()];
    }
}
