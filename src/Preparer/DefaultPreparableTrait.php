<?php

declare(strict_types = 1);

namespace Zfegg\PsrMvc\Preparer;

use Psr\Http\Message\ResponseInterface;

/**
 * @property \Psr\Http\Message\ResponseFactoryInterface $responseFactory
 */
trait DefaultPreparableTrait
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
