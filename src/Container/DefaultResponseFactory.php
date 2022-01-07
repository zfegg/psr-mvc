<?php

declare(strict_types = 1);

namespace Zfegg\PsrMvc\Container;

use Zfegg\PsrMvc\PrepareResponse\DefaultResponse;

class DefaultResponseFactory
{

    public function __invoke(): DefaultResponse
    {
        return new DefaultResponse();
    }
}
