<?php

declare(strict_types = 1);

namespace Zfegg\PsrMvc\Container;

use Psr\Container\ContainerInterface;
use Zfegg\PsrMvc\FormatMatcher;

class FormatMatcherFactory
{

    public function __invoke(ContainerInterface $container): FormatMatcher
    {
        $formats = $container->get('config')[FormatMatcher::class]['formats'] ?? null;
        return new FormatMatcher(
            $formats,
        );
    }
}
