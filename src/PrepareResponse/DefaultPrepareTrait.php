<?php

namespace Zfegg\PsrMvc\PrepareResponse;

use Psr\Http\Message\ResponseInterface;

/**
 * @property \Psr\Http\Message\ResponseFactoryInterface $responseFactory
 */
trait DefaultPrepareTrait
{

    private function defaultPrepare(mixed $result): ?ResponseInterface
    {
        if ($result instanceof ResponseInterface) {
            return $result;
        }

        if ($result === null) {
            return $this->responseFactory->createResponse(204);
        }

        return null;
    }
}