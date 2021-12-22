<?php

namespace ZfeggTest\CallableHandlerDecorator;

use Laminas\ConfigAggregator\ConfigAggregator;
use Laminas\Diactoros\ResponseFactory;
use Laminas\ServiceManager\AbstractFactory\ReflectionBasedAbstractFactory;
use Laminas\ServiceManager\ServiceManager;
use Mezzio\Application;
use Mezzio\MiddlewareFactory;
use Mezzio\Router\Middleware\DispatchMiddleware;
use Mezzio\Router\Middleware\RouteMiddleware;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeZoneNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;
use Zfegg\CallableHandlerDecorator\ConfigProvider;
use Zfegg\CallableHandlerDecorator\Router\RouteMetadata;
use Zfegg\ExpressiveTest\AbstractActionTestCase;

abstract class AbstractTestCase extends AbstractActionTestCase
{

    protected function getProjectDir(): string
    {
        return realpath(__DIR__ . '/');
    }

    public function loadContainer(): ContainerInterface
    {
        if ($this->container) {
            return $this->container;
        }

        $this->container = new ServiceManager();

        $providers = [
            \Mezzio\ConfigProvider::class,
            \Mezzio\Router\ConfigProvider::class,
            \Mezzio\Router\FastRouteRouter\ConfigProvider::class,
            ConfigProvider::class,
            (function () {
                return [
                    'dependencies' => [
                        'services' => [
                            SerializerInterface::class => new Serializer(
                                [
                                    new DateTimeZoneNormalizer(),
                                    new DateTimeNormalizer(),
                                    //                    new ObjectNormalizer(
                                    //                        null,
                                    //                        new CamelCaseToSnakeCaseNameConverter(),
                                    //                        null,
                                    //                    ),
                                ],
                                [
                                    new JsonEncoder(),
                                    new CsvEncoder(),
                                ]
                            ),
                            CacheItemPoolInterface::class => new ArrayAdapter(),
                            ResponseFactoryInterface::class => new ResponseFactory(),
                            'foo' => 'foo-value',
                        ]
                    ],
                    RouteMetadata::class => [
                        'paths' => [
                            __DIR__ . '/Example'
                        ]
                    ]
                ];
            })
        ];

        $aggregator = new ConfigAggregator($providers);
        $config = $aggregator->getMergedConfig();
        $this->container->setService('config', $config);
        $this->container->configure($config['dependencies']);
        $this->container->addDelegator(
            Application::class,
            function ($container, $name, callable $callback) {

                /** @var Application $app */
                $app = $callback();
                $factory = $container->get(MiddlewareFactory::class);
                $app->pipe(RouteMiddleware::class);
                $app->pipe(DispatchMiddleware::class);

                return $app;
            }
        );
        $this->container->addAbstractFactory(ReflectionBasedAbstractFactory::class);

        return $this->container;
    }

}