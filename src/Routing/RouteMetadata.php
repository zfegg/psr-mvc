<?php

declare(strict_types = 1);

namespace Zfegg\PsrMvc\Routing;

use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RecursiveRegexIterator;
use ReflectionClass;
use ReflectionMethod;
use RegexIterator;
use Zfegg\PsrMvc\Attribute\Route;
use Zfegg\PsrMvc\Attribute\RouteGroup;

class RouteMetadata
{
    /**
     * The paths where to look for mapping files.
     *
     * @var string[]
     */
    private array $paths = [];

    /**
     * The paths excluded from path where to look for mapping files.
     *
     * @var string[]
     */
    private array $excludePaths = [];

    /**
     * The file extension of mapping documents.
     */
    private string $fileExtension;

    /**
     * Cache for AnnotationDriver#getAllClassNames().
     *
     * @var string[]|null
     * @psalm-var list<class-string>|null
     */
    private ?array $classNames = null;

    /**
     * @var array[]
     */
    private array $groups = [];

    private ParameterConverterInterface $parameterConverter;

    private ?string $cacheFile;

    public const CACHE_TEMPLATE = <<<EOT
        <?php
        return %s;
        EOT;

    /**
     * @param string[] $paths
     * @param string[] $excludePaths
     * @param array[]  $groups
     */
    public function __construct(
        array $paths = [],
        array $excludePaths = [],
        string $fileExtension = 'Controller.php',
        array $groups = [],
        ?ParameterConverterInterface $parameterConverter = null,
        ?string $cacheFile = null,
    ) {
        $this->addPaths($paths);
        $this->addExcludePaths($excludePaths);
        $this->fileExtension = $fileExtension;
        $this->parameterConverter = $parameterConverter ?? new SlugifyParameterConverter();
        $this->groups = $groups;
        $this->cacheFile = $cacheFile;
    }

    public function addGroup(string $name, array $group): void
    {
        $this->groups[$name] = $group;
    }

    /**
     * Appends lookup paths to metadata driver.
     *
     * @param string[] $paths
     *
     */
    public function addPaths(array $paths): void
    {
        $this->paths = array_unique(array_merge($this->paths, $paths));
    }

    /**
     * Retrieves the defined metadata lookup paths.
     *
     * @return string[]
     */
    public function getPaths(): array
    {
        return $this->paths;
    }

    /**
     * Append exclude lookup paths to metadata driver.
     *
     * @param string[] $paths
     *
     */
    public function addExcludePaths(array $paths): void
    {
        $this->excludePaths = array_unique(array_merge($this->excludePaths, $paths));
    }

    /**
     * Retrieve the defined metadata lookup exclude paths.
     *
     * @return string[]
     */
    public function getExcludePaths(): array
    {
        return $this->excludePaths;
    }

    /**
     * Gets the file extension used to look for mapping files under.
     */
    public function getFileExtension(): string
    {
        return $this->fileExtension;
    }

    /**
     * Sets the file extension used to look for mapping files under.
     */
    public function setFileExtension(string $fileExtension): void
    {
        $this->fileExtension = $fileExtension;
    }

    public function getAllClassNames(): array
    {
        if ($this->classNames !== null) {
            return $this->classNames;
        }

        $classes       = [];
        $includedFiles = [];

        foreach ($this->paths as $path) {
            if (! is_dir($path)) {
                continue;
            }

            $iterator = new RegexIterator(
                new RecursiveIteratorIterator(
                    new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS),
                    RecursiveIteratorIterator::LEAVES_ONLY
                ),
                '/^.+' . preg_quote($this->fileExtension) . '$/i',
                RecursiveRegexIterator::GET_MATCH
            );

            foreach ($iterator as $file) {
                $sourceFile = $file[0];

                if (! preg_match('(^phar:)i', $sourceFile)) {
                    $sourceFile = realpath($sourceFile);
                }

                foreach ($this->excludePaths as $excludePath) {
                    $realExcludePath = realpath($excludePath);
                    assert($realExcludePath !== false);
                    $exclude = str_replace('\\', '/', $realExcludePath);
                    $current = str_replace('\\', '/', $sourceFile);

                    if (strpos($current, $exclude) !== false) {
                        continue 2;
                    }
                }

                require_once $sourceFile;

                $includedFiles[] = $sourceFile;
            }
        }

        $declared = get_declared_classes();

        foreach ($declared as $className) {
            $rc         = new ReflectionClass($className);
            $sourceFile = $rc->getFileName();
            if (! in_array($sourceFile, $includedFiles)) {
                continue;
            }

            $classes[] = $className;
        }

        $this->classNames = $classes;

        return $classes;
    }

    /**
     * @return array[Route, [string, string]][]
     * @throws \ReflectionException
     */
    public function getRoutes(): array
    {
        if ($cachedRoutes = $this->loadCachedRoutes()) {
            return $cachedRoutes;
        }

        $classes = $this->getAllClassNames();
        $routes = [];

        foreach ($classes as $className) {
            $ref = new ReflectionClass($className);
            $baseRoutes = [];
            $routeToken = [];

            /** @var RouteGroup $routeGroupAttr */
            $routeGroupAttr = null;
            foreach ($ref->getAttributes(RouteGroup::class) as $classAttrRef) {
                $routeGroupAttr = $classAttrRef->newInstance();
                break;
            }

            foreach ($ref->getAttributes(Route::class) as $classAttrRef) {
                $baseRoutes[] = $classAttrRef->newInstance();
            }

            $routeToken['[controller]'] = $this->parameterConverter->convertClassNameToPath($className);

            foreach ($ref->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
                foreach ($method->getAttributes(Route::class, 2) as $methodAttrRef) {
                    /** @var Route $routeAttr */
                    $routeAttr = $methodAttrRef->newInstance();
                    $routeAttr->options['controller'] = $className;
                    $routeAttr->options['action'] = $method->getName();

                    $routeToken['[action]'] = $this->parameterConverter->convertMethodToPath($method->getName());

                    $group = $this->groups[$routeGroupAttr?->name] ?? null;
                    if ($baseRoutes) {
                        foreach ($baseRoutes as $baseRoute) {
                            $newRouteAttr = $this->mergeRoute(
                                $routeToken,
                                $routeAttr,
                                $baseRoute,
                                $group,
                            );
                            $routes[] = [$newRouteAttr, [$className, $method->getName()], $group];
                        }
                    } else {
                        $newRouteAttr = $this->mergeRoute(
                            $routeToken,
                            $routeAttr,
                            null,
                            $group,
                        );
                        $routes[] = [$newRouteAttr, [$className, $method->getName()], $group];
                    }
                }
            }
        }

        $this->cacheRoutes($routes);

        return $routes;
    }

    private function mergeRoute(
        array $replacePairs,
        Route $route,
        ?Route $baseRoute = null,
        ?array $group = null
    ): Route {
        $route = clone $route;

        if ($baseRoute && ! str_starts_with($route->path, '/')) {
            $route->path = $baseRoute->path . ($route->path ? '/' . $route->path : '');
            $route->options = array_merge($baseRoute->options, $route->options);
            $route->middlewares = array_merge($baseRoute->middlewares, $route->middlewares);

            if (! $route->name && $baseRoute->name) {
                $route->name = $baseRoute->name;
            }
        }

        if ($group) {
            $route->path = $group['prefix'] . $route->path;
            $route->middlewares = array_merge($group['middlewares'], $route->middlewares);

            if ($route->name && isset($group['name'])) {
                $route->name = $group['name'] . $route->name;
            }
        }

        if ($route->name !== null) {
            $route->name = strtr($route->name, $replacePairs);
        }
        $route->path = strtr($route->path, $replacePairs);

        return $route;
    }


    /**
     * Load routes from cache
     *
     */
    private function loadCachedRoutes(): ?array
    {
        if (! $this->cacheFile) {
            return null;
        }
        set_error_handler(static function (): void {
        }, E_WARNING); // suppress php warnings
        $routes = include $this->cacheFile;
        restore_error_handler();

        // Cache file does not exist
        if (! is_array($routes)) {
            return null;
        }

        foreach ($routes as &$route) {
            $route[0] = new Route(...$route[0]);
        }

        return $routes;
    }

    /**
     * Save routes to cache
     */
    private function cacheRoutes(array $routes): void
    {
        if (! $this->cacheFile) {
            return ;
        }

        foreach ($routes as &$route) {
            $route[0] = get_object_vars($route[0]);
        }

        file_put_contents(
            $this->cacheFile,
            sprintf(self::CACHE_TEMPLATE, var_export($routes, true))
        );
    }
}
