<?php


namespace Zfegg\CallableHandlerDecorator\Router;


use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RecursiveRegexIterator;
use ReflectionClass;
use ReflectionMethod;
use RegexIterator;
use Zfegg\CallableHandlerDecorator\Attribute\Route;

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
    private string $fileExtension = '.php';

    /**
     * Cache for AnnotationDriver#getAllClassNames().
     *
     * @var string[]|null
     * @psalm-var list<class-string>|null
     */
    private ?array $classNames = null;


    private ParameterTransformer $parameterTransformer;

    public function __construct(
        array $paths = [],
        array $excludePaths = [],
        string $fileExtension = '.php',
        ParameterTransformer $parameterTransformer = null,
    )
    {
        $this->addPaths($paths);
        $this->addExcludePaths($excludePaths);
        $this->fileExtension = $fileExtension;
        $this->parameterTransformer = $parameterTransformer ?? new SlugifyParameterTransformer();
    }

    /**
     * Appends lookup paths to metadata driver.
     *
     * @param string[] $paths
     *
     * @return void
     */
    public function addPaths(array $paths)
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
     * @return void
     */
    public function addExcludePaths(array $paths)
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
                throw new \InvalidArgumentException('Invalid path ' . $path);
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
        $classes = $this->getAllClassNames();

        $baseRoutes = [];
        $routes = [];
        foreach ($classes as $className) {
            $ref = new ReflectionClass($className);
            foreach ($ref->getAttributes(Route::class) as $classAttrRef) {
                $baseRoutes[] = $classAttrRef->newInstance();
            }
            foreach ($ref->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {

                foreach ($method->getAttributes(Route::class, 2) as $methodAttrRef) {
                    /** @var Route $routeAttr */
                    $routeAttr = $methodAttrRef->newInstance();
                    $routeAttr->options['controller'] = $className;
                    $routeAttr->options['action'] = $method->getName();

                    $routeTokenReplace = $this->parameterTransformer->transform($className, $method->getName());

                    if ($baseRoutes) {
                        foreach ($baseRoutes as $baseRoute) {
                            $newRouteAttr = $this->mergeRoute($routeAttr, $baseRoute);
                            $this->convertRouteToken($newRouteAttr, $routeTokenReplace);
                            $routes[] = [$newRouteAttr, [$className, $method->getName()]];
                        }
                    } else {
                        $this->convertRouteToken($routeAttr, $routeTokenReplace);
                        $routes[] = [$routeAttr, [$className, $method->getName()]];
                    }
                }
            }
        }

        return $routes;
    }

    private function mergeRoute(Route $route, Route $baseRoute): Route
    {
        $route = clone $route;
        if (! $route->path) {
            $route->path = $baseRoute->path;
        } elseif ($route->path[0] != '/') {
            $route->path = $baseRoute->path . '/' . $route->path;
        }

        $route->options = array_merge($baseRoute->options, $route->options);
        $route->middlewares = array_merge($baseRoute->middlewares, $route->middlewares);

        if (! $route->name && $baseRoute->name) {
            $route->name = $baseRoute->name;
        }

        return $route;
    }

    private function convertRouteToken(Route $route, array $replacePairs): void
    {
        $route->name = strtr($route->name, $replacePairs);
        $route->path = strtr($route->path, $replacePairs);
    }
}
