<?php

declare(strict_types = 1);

namespace Zfegg\PsrMvc\Container;

use Psr\Container\ContainerInterface;
use Zfegg\PsrMvc\Routing\SlugifyParameterConverter;

class SlugifyParameterConverterFactory
{

    public function __invoke(ContainerInterface $container): SlugifyParameterConverter
    {
        $config = $container->get('config')[SlugifyParameterConverter::class] ?? [];
        return new SlugifyParameterConverter(
            ...$config
        );
    }
}
