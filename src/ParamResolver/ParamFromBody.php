<?php

declare(strict_types = 1);

namespace Zfegg\PsrMvc\ParamResolver;

use LogicException;
use Psr\Http\Message\ServerRequestInterface;
use ReflectionParameter;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class ParamFromBody implements ParamResolverInterface
{
    private ?DenormalizerInterface $serializer;

    public function __construct(?DenormalizerInterface $serializer = null)
    {
        $this->serializer = $serializer;
    }

    public function resolve(object $attr, ReflectionParameter $parameter): callable
    {
        /** @var \Zfegg\PsrMvc\Attribute\FromBody $attr */

        if ($parameter->hasType() && ! $parameter->getType()->isBuiltin()) {
            $type = $parameter->getType()->getName();
            $serializer = $this->serializer;
            if (! $serializer) {
                throw new LogicException(
                    'You must register at least one normalizer to be able to denormalize objects.'
                );
            }
            return static function (ServerRequestInterface $request) use ($serializer, $type, $attr) {
                return $serializer->denormalize(
                    $request->getParsedBody(),
                    $type,
                    context: $attr->serializerContext,
                );
            };
        } else {
            $name = $attr->name ?? $parameter->getName();
            $default = $parameter->isDefaultValueAvailable() ? $parameter->getDefaultValue() : null;

            return $attr->root
                ? static fn(ServerRequestInterface $request): null|array|object => $request->getParsedBody()
                : static fn(ServerRequestInterface $request): mixed => $request->getParsedBody()[$name] ?? $default;
        }
    }
}
