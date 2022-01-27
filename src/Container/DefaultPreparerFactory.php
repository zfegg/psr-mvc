<?php

declare(strict_types = 1);

namespace Zfegg\PsrMvc\Container;

use Zfegg\PsrMvc\Preparer\DefaultPreparer;

class DefaultPreparerFactory
{

    public function __invoke(): DefaultPreparer
    {
        return new DefaultPreparer();
    }
}
