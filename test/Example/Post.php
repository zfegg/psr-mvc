<?php

declare(strict_types = 1);

namespace ZfeggTest\PsrMvc\Example;

class Post
{

    private int $foo;

    private int $bar;

    public function getBar(): int
    {
        return $this->bar;
    }

    public function setBar(int $bar): void
    {
        $this->bar = $bar;
    }

    public function getFoo(): int
    {
        return $this->foo;
    }

    public function setFoo(int $foo): void
    {
        $this->foo = $foo;
    }
}
