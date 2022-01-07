<?php

namespace Zfegg\PsrMvc\Container;

use Zfegg\PsrMvc\PrepareResponse\DefaultResponse;

class DefaultResponseFactory
{

    public function __invoke(): DefaultResponse
    {
        return new DefaultResponse();
    }
}