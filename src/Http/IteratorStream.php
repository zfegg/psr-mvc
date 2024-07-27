<?php

declare(strict_types = 1);

namespace Zfegg\PsrMvc\Http;

use BadMethodCallException;
use Countable;
use IteratorAggregate;
use Traversable;
use Psr\Http\Message\StreamInterface;

/**
 * Iterator-based stream implementation.
 *
 * Wraps an iterator to allow seeking, reading, and casting to string.
 *
 * Keys are ignored, and content is concatenated without separators.
 */
class IteratorStream implements StreamInterface
{
    private ?Traversable $iterator;

    /**
     * Current position in iterator
     */
    private int $position = 0;

    /**
     * Construct a stream instance using an iterator.
     *
     * If the iterator is an IteratorAggregate, pulls the inner iterator
     * and composes that instead, to ensure we have access to the various
     * iterator capabilities.
     *
     */
    public function __construct(Traversable $iterator)
    {
        if ($iterator instanceof IteratorAggregate) {
            $iterator = $iterator->getIterator();
        }
        $this->iterator = $iterator;
    }

    /**
     * @inheritdoc
     */
    public function __toString(): string
    {
        $this->iterator->rewind();

        return $this->getContents();
    }

    /**
     * @inheritdoc
     */
    public function close(): void
    {
    }

    /**
     * @inheritdoc
     */
    public function detach()
    {
        $iterator = $this->iterator;
        $this->iterator = null;
        return $iterator;
    }

    /**
     * @inheritdoc
     */
    public function getSize(): ?int
    {
        if ($this->iterator instanceof Countable) {
            return count($this->iterator);
        }

        return null;
    }

    /**
     * @inheritdoc
     */
    public function tell(): int
    {
        return $this->position;
    }

    /**
     * @inheritdoc
     */
    public function eof(): bool
    {
        if ($this->iterator instanceof Countable) {
            return ($this->position === count($this->iterator));
        }

        return (! $this->iterator->valid());
    }

    /**
     * @inheritdoc
     */
    public function isSeekable(): bool
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function seek($offset, $whence = SEEK_SET): void
    {
        if (! is_int($offset) && ! is_numeric($offset)) {
            return ;
        }
        $offset = (int) $offset;

        if ($offset < 0) {
            return ;
        }

        $key = $this->iterator->key();
        if (! is_int($key) && ! is_numeric($key)) {
            $key = 0;
            $this->iterator->rewind();
        }

        if ($key >= $offset) {
            $key = 0;
            $this->iterator->rewind();
        }

        while ($this->iterator->valid() && $key < $offset) {
            $this->iterator->next();
            ++$key;
        }

        $this->position = $key;
    }

    /**
     * @inheritdoc
     */
    public function rewind(): void
    {
        $this->iterator->rewind();
        $this->position = 0;
    }

    /**
     * @inheritdoc
     */
    public function isWritable(): bool
    {
        return false;
    }

    /**
     * @inheritdoc
     */
    public function write($string): int
    {
        throw new BadMethodCallException("Write method not impl.");
    }

    /**
     * @inheritdoc
     */
    public function isReadable(): bool
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function read($length): string
    {
        $buf = (string)$this->iterator->current();
        $this->iterator->next();
        ++$this->position;

        return $buf;
    }

    /**
     * @inheritdoc
     */
    public function getContents(): string
    {
        $contents = '';
        while ($this->iterator->valid()) {
            $contents .= $this->read(PHP_INT_MAX);
        }
        return $contents;
    }

    /**
     * @inheritdoc
     */
    public function getMetadata($key = null)
    {
        return ($key === null) ? [] : null;
    }
}
