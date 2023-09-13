<?php

declare(strict_types = 1);

namespace ZfeggTest\PsrMvc;

use Laminas\ConfigAggregator\ConfigAggregator;
use Laminas\Diactoros\ResponseFactory;
use Laminas\ServiceManager\ServiceManager;
use Mezzio\Application;
use Mezzio\MiddlewareFactory;
use Mezzio\Router\Middleware\DispatchMiddleware;
use Mezzio\Router\Middleware\RouteMiddleware;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeZoneNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;
use Zfegg\ExpressiveTest\AbstractActionTestCase;
use Zfegg\ExpressiveTest\PassMiddleware;
use Zfegg\PsrMvc\ConfigProvider;
use Zfegg\PsrMvc\Routing\RouteMetadata;

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
            \Laminas\Di\ConfigProvider::class,
            (function () {
                return [
                    'dependencies' => [
                        'services' => [
                            Serializer::class => new Serializer(
                                [
                                    new DateTimeZoneNormalizer(),
                                    new DateTimeNormalizer(),
                                    new ObjectNormalizer(
                                        null,
                                        new CamelCaseToSnakeCaseNameConverter(),
                                        null,
                                    ),
                                ],
                                [
                                    new JsonEncoder(),
                                    new CsvEncoder(),
                                ]
                            ),
                            ResponseFactoryInterface::class => new ResponseFactory(),
                            'foo' => 'foo-value',
                            'middleware1' => new PassMiddleware(),
                        ],
                        'aliases' => [
                            SerializerInterface::class => Serializer::class,
                        ],
                        'auto' => [
                            'types' => [
                                RouteMetadata::class => [
                                    'parameters' => [
                                        'paths' => glob(dirname(__DIR__) . '/*/Example'),
                                        'groups' => [
                                            'test' => [
                                                'prefix' => '/api',
                                                'middlewares' => [
                                                    'middleware1',
                                                ],
                                                'name' => 'api.test.'
                                            ]
                                        ],
                                    ]
                                ]
                            ]
                        ],
                    ],
                ];
            })
        ];

        $aggregator = new ConfigAggregator($providers);
        $config = $aggregator->getMergedConfig();
        $this->container->setService('config', new \ArrayObject($config));
        $this->container->setService(ContainerInterface::class, $this->container);
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

        return $this->container;
    }
}
