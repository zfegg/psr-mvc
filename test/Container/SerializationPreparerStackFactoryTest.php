<?php

declare(strict_types = 1);

namespace ZfeggTest\PsrMvc\Container;

use Zfegg\PsrMvc\Container\SerializationPreparerStackFactory;
use Zfegg\PsrMvc\Preparer\PreparerStack;

use Zfegg\PsrMvc\Preparer\SerializationPreparer;
use ZfeggTest\PsrMvc\AbstractTestCase;

class SerializationPreparerStackFactoryTest extends AbstractTestCase
{

    public function testInvoke(): void
    {
        $this->container->setFactory(PreparerStack::class, SerializationPreparerStackFactory::class);
        $preparer = $this->container->get(PreparerStack::class);

        $this->assertInstanceOf(SerializationPreparer::class, $preparer[1]);
    }
}
