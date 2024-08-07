<?php

declare(strict_types = 1);

namespace Zfegg\PsrMvc\ParamResolver;

use Laminas\ServiceManager\AbstractPluginManager;
use Laminas\ServiceManager\Exception\InvalidServiceException;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use ReflectionNamedType;
use ReflectionParameter;
use Symfony\Component\Serializer\Serializer;
use Zfegg\PsrMvc\Attribute\FromAttribute;
use Zfegg\PsrMvc\Attribute\FromBody;
use Zfegg\PsrMvc\Attribute\FromContainer;
use Zfegg\PsrMvc\Attribute\FromCookie;
use Zfegg\PsrMvc\Attribute\FromHeader;
use Zfegg\PsrMvc\Attribute\FromQuery;
use Zfegg\PsrMvc\Attribute\FromServer;
use Zfegg\PsrMvc\Attribute\ParamResolverAttributeInterface;
use Zfegg\PsrMvc\Routing\ParameterConverterInterface;

/**
 *
 * @method ParamResolverInterface get($name, ?array $options = null)
 */
class ParamResolverManager extends AbstractPluginManager
{

    /**
     * @var string
     */
    protected $instanceOf = ParamResolverInterface::class; // phpcs:ignore

    public function __construct(private ContainerInterface $container, array $config = [])
    {
        parent::__construct($container, $config);
        $this->configure([
            'factories' => [
                FromAttribute::class => static fn(ContainerInterface $container) =>
                    new ParamFromAttribute($container->get(ParameterConverterInterface::class)),
                FromQuery::class              => static fn(ContainerInterface $container) =>
                    new ParamFromQuery($container->get(ParameterConverterInterface::class)),
                FromBody::class               => static fn(ContainerInterface $container) =>
                    new ParamFromBody(
                        $container->get(ParameterConverterInterface::class),
                        $container->has(Serializer::class) ? $container->get(Serializer::class) : null
                    ),
                FromContainer::class          => static fn(ContainerInterface $container) =>
                    new ParamFromContainer($container),
                FromCookie::class             => static fn(ContainerInterface $container) =>
                    new ParamFromCookie($container->get(ParameterConverterInterface::class)),
                FromHeader::class             => static fn() => new ParamFromHeader(),
                FromServer::class             => static fn() => new ParamFromServer(),
            ]
        ]);
    }


    /**
     * Retrieve param resolver.
     */
    public function resolver(ReflectionParameter $parameter): callable
    {
        $attrs = $parameter->getAttributes(ParamResolverAttributeInterface::class, 2);

        foreach ($attrs as $attrRef) {
            $attr = $attrRef->newInstance();
            $this->has($attrRef->getName());
            return $this->get($attrRef->getName())->resolve($attr, $parameter);
        }

        $type = $parameter->getType();
        $type = $type instanceof ReflectionNamedType ? $type->getName() : null;

        if ($type === ServerRequestInterface::class) {
            return static fn(ServerRequestInterface $request): ServerRequestInterface => $request;
        }

        if ($type === 'array' ||
            $type === null ||
            (is_string($type) && ! class_exists($type) && ! interface_exists($type))
        ) {
            return $this->get(FromAttribute::class)->resolve(new FromAttribute(), $parameter);
        }

        $name = $parameter->getName();
        $container = $this->container;
        $hasService = $this->container->has($type);

        return static function (ServerRequestInterface $request) use ($name, $type, $hasService, $container) {
            $value = $request->getAttribute($type, $request->getAttribute($name));
            if ($value === null && $hasService) {
                return $container->get($type);
            }

            return $value;
        };
    }

    public function validate(mixed $instance): void
    {
        if ($instance instanceof $this->instanceOf) {
            return;
        }

        throw new InvalidServiceException(sprintf(
            'Plugin manager "%s" expected an instance of type "%s", but "%s" was received',
            static::class,
            $this->instanceOf,
            get_debug_type($instance)
        ));
    }
}
