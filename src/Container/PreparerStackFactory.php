<?php

declare(strict_types = 1);

namespace Zfegg\PsrMvc\Container;

use Psr\Container\ContainerInterface;
use Zfegg\PsrMvc\Preparer\CommonPreparer;
use Zfegg\PsrMvc\Preparer\DefaultPreparer;
use Zfegg\PsrMvc\Preparer\PreparerStack;
use Zfegg\PsrMvc\Preparer\ResultPreparableInterface;

class PreparerStackFactory
{

    public function __invoke(ContainerInterface $container): ResultPreparableInterface
    {
        $preparer = new PreparerStack();
        $preparer->push($container->get(DefaultPreparer::class));
        $preparer->push($container->get(CommonPreparer::class));

        return $preparer;
    }
}
