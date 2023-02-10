<?php

declare(strict_types = 1);

namespace Zfegg\PsrMvc\Http\Emitter;

use Laminas\HttpHandlerRunner\Emitter\EmitterInterface;
use Laminas\HttpHandlerRunner\Emitter\SapiEmitterTrait;
use Psr\Http\Message\ResponseInterface;
use Zfegg\PsrMvc\Http\IteratorStream;

use function flush;
use function connection_aborted;
use function ob_start;
use function ob_flush;
use function ob_end_flush;

/**
 * @psalm-type ParsedRangeType = array{0:string,1:int,2:int,3:'*'|int}
 */
class SapiIteratorStreamEmitter implements EmitterInterface
{
    use SapiEmitterTrait;

    /**
     * Emits a response for a PHP SAPI environment.
     *
     * Emits the status line and headers via the header() function, and the
     * body content via the output buffer.
     */
    public function emit(ResponseInterface $response): bool
    {
        $body = $response->getBody();

        if (! $body instanceof IteratorStream) {
            return false;
        }

        $this->assertNoPreviousOutput();

        $this->emitHeaders($response);
        $this->emitStatusLine($response);

        if (ob_get_level() == 0) {
            ob_start();
        }

        flush();

        if ($body->isSeekable()) {
            $body->rewind();
        }

        while (! $body->eof()) {
            echo $body->read(PHP_INT_MAX);

            if (connection_aborted()) {
                break;
            }

            ob_flush();
            flush();
        }

        ob_end_flush();
        return true;
    }
}
