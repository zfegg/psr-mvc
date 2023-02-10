<?php

declare(strict_types = 1);

namespace Zfegg\PsrMvc\Http;

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
    public function __toString()
    {
        $this->iterator->rewind();

        return $this->getContents();
    }

    /**
     * @inheritdoc
     */
    public function close()
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
    public function getSize()
    {
        if ($this->iterator instanceof Countable) {
            return count($this->iterator);
        }

        return null;
    }

    /**
     * @inheritdoc
     */
    public function tell()
    {
        return $this->position;
    }

    /**
     * @inheritdoc
     */
    public function eof()
    {
        if ($this->iterator instanceof Countable) {
            return ($this->position === count($this->iterator));
        }

        return (! $this->iterator->valid());
    }

    /**
     * @inheritdoc
     */
    public function isSeekable()
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function seek($offset, $whence = SEEK_SET)
    {
        if (! is_int($offset) && ! is_numeric($offset)) {
            return false;
        }
        $offset = (int) $offset;

        if ($offset < 0) {
            return false;
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
        return true;
    }

    /**
     * @inheritdoc
     */
    public function rewind()
    {
        $this->iterator->rewind();
        $this->position = 0;
        return true;
    }

    /**
     * @inheritdoc
     */
    public function isWritable()
    {
        return false;
    }

    /**
     * @inheritdoc
     */
    public function write($string)
    {
        return false;
    }

    /**
     * @inheritdoc
     */
    public function isReadable()
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function read($length)
    {
        $buf = (string)$this->iterator->current();
        $this->iterator->next();
        ++$this->position;

        return $buf;
    }

    /**
     * @inheritdoc
     */
    public function getContents()
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
