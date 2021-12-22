<?php declare(strict_types = 1);

namespace ZfeggTest\PsrMvc;

use Laminas\ServiceManager\ServiceManager;
use Zfegg\PsrMvc\ConfigProvider;
use PHPUnit\Framework\TestCase;
use Zfegg\PsrMvc\CallbackHandlerFactory;

class ConfigProviderTest extends TestCase
{

    public function testInvoke(): void
    {
        $container = new ServiceManager((new ConfigProvider())()['dependencies']);

        $this->assertTrue($container->has(CallbackHandlerFactory::class));
    }
}
