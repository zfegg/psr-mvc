<?php

declare(strict_types = 1);

namespace Zfegg\PsrMvc\Container;

use Negotiation\Negotiator;
use Psr\Container\ContainerInterface;
use Zfegg\PsrMvc\FormatMatcher;

class FormatMatcherFactory
{

    public function __invoke(ContainerInterface $container): FormatMatcher
    {
        $formats = $container->get('config')[FormatMatcher::class] ?? FormatMatcher::MIME_TYPES;
        return new FormatMatcher(
            $container->get(Negotiator::class),
            $formats,
        );
    }
}
