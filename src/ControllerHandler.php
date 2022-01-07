<?php

declare(strict_types = 1);

namespace Zfegg\PsrMvc;

use Mezzio\Router\RouteResult;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zfegg\PsrMvc\Routing\ParameterConverterInterface;

class ControllerHandler implements RequestHandlerInterface
{
    /**
     * @var CallbackHandler[]
     */
    private array $handlers = [];

    public function __construct(
        private CallbackHandlerFactory $factory,
        private ParameterConverterInterface $parameterConverter,
        private RequestHandlerInterface $notFoundHandler
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $defaultController = null;
        $defaultAction = null;

        $result = $request->getAttribute(RouteResult::class);
        if ($result) {
            $options = $result->getMatchedRoute()->getOptions();
            $defaultAction = $options['action'] ?? null;
            $defaultController = $options['controller'] ?? null;
        }

        $controller = $request->getAttribute('controller', $defaultController);
        $action = $request->getAttribute('action', $defaultAction);
        $name = $controller . $this->factory->getSeparator() . $action;

        if (! isset($this->handlers[$name])) {
            $callback = $controller
                . $this->factory->getSeparator()
                . $this->parameterConverter->convertActionToMethod($action);

            $this->handlers[$name] = $this->factory->exists($callback)
                ? $this->factory->create($callback)
                : $this->notFoundHandler;
        }

        return $this->handlers[$name]->handle($request);
    }
}
