<?php

declare(strict_types = 1);

namespace Zfegg\PsrMvc\Container;

use Mezzio\Container\ErrorResponseGeneratorFactory as MezzioErrorResponseGeneratorFactory;
use Psr\Container\ContainerInterface;
use Zfegg\PsrMvc\ErrorHandler\ErrorResponseGenerator;

class ErrorResponseGeneratorFactory
{

    public function __invoke(ContainerInterface $container): ErrorResponseGenerator
    {
        $config = $container->has('config') ? $container->get('config') : [];
        return new ErrorResponseGenerator(
            (new MezzioErrorResponseGeneratorFactory)($container),
            $config['debug'] ?? false,
        );
    }
}
