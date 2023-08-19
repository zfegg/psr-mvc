English | [简体中文](README.zh-CN.md)

PSR MVC handler
================

[![GitHub Actions: Run tests](https://github.com/zfegg/psr-mvc/workflows/qa/badge.svg)](https://github.com/zfegg/psr-mvc/actions?query=workflow%3A%22qa%22)
[![Coverage Status](https://coveralls.io/repos/github/zfegg/psr-mvc/badge.svg?branch=main)](https://coveralls.io/github/zfegg/psr-mvc?branch=main)
[![Latest Stable Version](https://poser.pugx.org/zfegg/psr-mvc/v/stable.png)](https://packagist.org/packages/zfegg/psr-mvc)

Using MVC style for PSR handler applications, like [dotnet core MVC](https://docs.microsoft.com/en-us/aspnet/core/mvc/controllers/routing?view=aspnetcore-6.0).  
Using the PHP attributes (annotations), convert the controller to PSR15 `RequestHandlerInterface` object.

Installation
------------

```bash
composer require zfegg/psr-mvc
```

Usage
------

Attributes usage like [dotnet core MVC](https://docs.microsoft.com/en-us/aspnet/core/mvc/controllers/routing?view=aspnetcore-6.0)

### MVC Route

#### Getting started with Mezzio

```php

// File config/config.php
// Add ConfigProvider 

new ConfigAggregator([
  Zfegg\PsrMvc\ConfigProvider::class,
]);
```

```php

// config/autoload/global.php
use Zfegg\PsrMvc\Container\HandlerFactory;

return [
    // Add scan controllers paths
    \Zfegg\PsrMvc\Routing\RouteMetadata::class => [
        'paths' => ['path/to/Controller'],
    ]
];

// path/to/Controller/HomeController.php
public class HomeController
{
    #[Route("/")]
    #[Route("/home")]
    #[Route("/home/index")]
    #[Route("/home/index/{id?}")]
    public index(?int $id)
    {
        return new HtmlResponse();
    }

    #[Route("/home/about")]
    #[Route("/home/about/{id}")]
    public about(?int $id)
    {
        return new HtmlResponse();
    }
}

```

#### Attributes

<!-- 支持的方法属性列表 -->

- `Route(string $path, array $middlewares = [], ?string $name = null, array $options = [], ?array $methods = null)`
- `HttpGet(string $path, array $middlewares = [], ?string $name = null, array $options = [])`
- `HttpPost(string $path, array $middlewares = [], ?string $name = null, array $options = [])`
- `HttpPatch(string $path, array $middlewares = [], ?string $name = null, array $options = [])`
- `HttpPut(string $path, array $middlewares = [], ?string $name = null, array $options = [])`
- `HttpDelete(string $path, array $middlewares = [], ?string $name = null, array $options = [])`
- `HttpHead(string $path, array $middlewares = [], ?string $name = null, array $options = [])`

Register routes by PHP attributes.

```php
return [
  RouteMetadata::class => [
    // Scan controller paths.
    'paths' => [
       'path/Controller',
    ],
  ],
]
```

The following code applies `#[Route("/[controller]/[action]")]` to the controller:

```php

public class HomeController
{
    #[Route("/")]
    #[Route("/home")]
    #[Route("/home/index")]
    #[Route("/home/index/{id?}")]
    public index(?int $id)
    {
        return new HtmlResponse();
    }

    #[Route("/home/about")]
    #[Route("/home/about/{id}")]
    public about(?int $id)
    {
        return new HtmlResponse();
    }
}
```

#### Combining attribute routes

```php
use Psr\Http\Message\ResponseInterface;

#[Route("/api/[controller]")] // Route prefix `/api/products`
class ProductsController {

    #[HttpGet]   // GET /api/products
    public function listProducts(): array {
        return $db->fetchAllProducts();
    }
    
    // Route path `/api/products/{id}`
    #[HttpGet('{id}')] // GET /api/products/123
    public function getProduct(int $id): object {
        return $db->find($id);
    }
    
    #[HttpPost]  // POST /api/products
    public function create(#[FromBody(root: true)] array $data): object {
        $db->save($data);
        // ...
        return $db->find($lastInsertId);
    }
}
```


### Wrap controller handler

#### Using param attributes

- `FromAttribute(?string $name = null)`
    - `$name` default is the parameter name
- `FromBody(?string $name = null, ?bool $root = false, array $serializerContext = [])`
    - `$name` default is the parameter name
- `FromContainer(?string $name = null)`
    - `$name` default is the parameter type
- `FromCookie(?string $name = null)`
    - `$name` default is the parameter name
- `FromHeader(?string $name = null)`
    - `$name` default is the parameter name
- `FromQuery(?string $name = null)`
    - `$name` default is the parameter name
- `FromServer(string $name)`

```php
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/*
POST /api/example/hello?page=1
Host: localhost
Cookie: PHPSESSION=xxx

name=foo
*/

class ExampleController {
    #[HttpPost('/api/[controller]/[action]')] 
    public function post(
        #[FromQuery]
        int $page,              // 1
        #[FromBody]
        string $name,           // "foo"
        #[FromContainer('db')]
        \PDO $container,        // object(PDO)
        #[FromCookie('PHPSESSION')]
        string $sessionId,      // "xxx"
        #[FromHeader]
        string $host,           // "localhost"
        #[FromServer('REMOTE_ADDR')]
        string $ip,             // "127.0.0.1"
    ): void {
        return ;
    }
    
    // Default binding params
    #[HttpPost('/api/[controller]/[action]/{id}')] 
    public function hello(
       ServerRequestInterface $request,  // Default bind `$request`.
       int $id,    // Default bind `$request->getAttribute('id')`.
       Foo $foo,   // If container exists the `Foo`, default bind `$container->get('id')`.
       Bar $bar,   // Default bind `$request->getAttribute(Bar::class, $request->getAttribute('bar'))`.
    ): void {
    }
}

```

#### Default param bindings

```php

class ExampleController {
    #[HttpPost('/api/[controller]/[action]/{id}')] 
    public function hello(
       ServerRequestInterface $request,  // Default bind `$request`.
       int $id,    // Default bind `$request->getAttribute('id')`.
       Foo $foo,   // If container exists the `Foo`, default bind `$container->get('id')`.
       Bar $bar,   // Default bind `$request->getAttribute(Bar::class, $request->getAttribute('bar'))`.
    ): void {
    }
}
```

### Prepare result to PSR response.

Resolves various types of method results convert to 'Psr\Http\Message\ResponseInterface'.
For resolve callback result to ResponseInterface.

#### `Zfegg\PsrMvc\Preparer\SerializationPreparer`


```php
class ExampleResponseController {
    #[HttpPost('/hello-void')]  // `void` -> HTTP 204 No Content
    public function helloVoid(): void {
    }

    /*
     *  If result is string, then convert to `HtmlResponse` object.
     *  `new HtmlResponse($result)`
     */
    #[HttpPost('/hello-string')]
    public function helloString(): string {
      return '<h1>Hello</h1>';
    }
    
    /*
     *  If result is array, default convert to `JsonResponse` object.
     *  `new JsonResponse($result)`
     */
    #[HttpPost('/hello-array')]
    public function helloArray(): array {
      return ['foo' => 'a', 'bar' => 'b'];
    }
}
```

#### `Zfegg\PsrMvc\Preparer\SerializationPreparer` (Recommend)

Serialize by `symfony/serializer` and write the response body.

```php
class ExampleResponseController {
    #[HttpPost('/hello-void')]  // `void` -> HTTP 204 No Content
    public function helloVoid(): void {
    }

    /*
     * Serialize by `symfony/serializer`.
     * The serialization format is parsed by `FormatMatcher`.
     * <code>
     * $result = $serializer->serialize($result, $format);
     * $response->withBody($result);
     * </code>
     */
    #[HttpPost('/hello-foo')]
    public function hello(): Foo {
     return new Foo();
    }
}
```


Preparer options:

| Key | description |
|---|--------------|
| status | Http response code. |
| headers | Http response headers. | 
| `<more>` | `$serializer->serialize` context variables. |

#### The `PrepareResult` attribute

Using `#[PrepareResult]` attribute to select a preparer and pass the context.

```php
use \Zfegg\PsrMvc\Preparer\SerializationPreparer;
use Zfegg\PsrMvc\Attribute\PrepareResult;

class ExampleResponseController {
    #[HttpPost('/hello-void')]  // `void` -> HTTP 204 No Content
    public function helloVoid(): void {
    }

    /*
     * 选用 `SerializationPreparer` 预处理器, 处理结果.
     */
    #[HttpPost('/hello-foo')]
    #[PrepareResult(SerializationPreparer::class, ['status' => 201, 'headers' => ['X-Test' => 'foo']])]
    public function hello(): Foo {
     return new Foo();
    }
}
```


### Example for Mezzio :

```php
// Class file HelloController.php

class HelloController {
  public function say(
    \Psr\Http\Message\ServerRequestInterface $request, // Inject request param
    string $name, // Auto inject param from $request->getAttribute('name').
    Foo $foo     // Auto inject param from container.
  ) {
    return new TextResponse('hello ' . $name);
  }
}
```

```php

// File config/config.php
// Add ConfigProvider 

new ConfigAggregator([
  Zfegg\PsrMvc\ConfigProvider::class,
]);
```

```php

// config/autoload/global.php
// Add demo class factories

use Zfegg\PsrMvc\Container\HandlerFactory;

return [
    'dependencies' => [
        'invokables' => [
            Hello::class,
        ],
        'factories' => [
            Hello::class . '@say' => HandlerFactory::class, 
        ],
    ]
];
```


### Register route without attribute.

Using `CallableHandlerAbstractFactory` register route.


```php

// config/autoload/global.php
// Add demo class factories

use Zfegg\PsrMvc\Container\CallbackHandlerAbstractFactory;

return [
    'dependencies' => [
        'factories' => [
            ExampleController::class . '@fooMethod' => CallbackHandlerAbstractFactory::class, 
        ],
    ]
];

$app->get('/foo-method', ExampleController::class . '@fooMethod')
```

Register abstract factory in `laminas/laminias-servicemanager`.


```php

// config/autoload/global.php
// Add demo class factories

use Zfegg\PsrMvc\Container\CallbackHandlerAbstractFactory;

return [
    'dependencies' => [
        'invokables' => [
            Hello::class,
        ],
        'abstract_factories' => [
            CallbackHandlerAbstractFactory::class, 
        ],
    ]
];

class User {
  function create() {}
  function getList() {}
  function get($id) {}
  function delete($id) {}
}

// CallableHandlerDecorator abstract factory.
$container->get('User@create');
$container->get('User@getList');
$container->get('User@get');
$container->get('User@delete');
```


### ErrorHandler for mezzio

Rich error handling, 

#### Response to json format

Throw exception in handler.

```php
use \Zfegg\PsrMvc\Exception\AccessDeniedHttpException;
use \Zfegg\PsrMvc\Attribute\HttpGet;

class FooController {
  #[HttpGet("/api/foo")]
  public function fooAction() {
    throw new AccessDeniedHttpException("Foo", code: 100);
  }
}

```

When request is ajax will response to json result:

```
HTTP/1.1 403 Forbidden

{"message":"Foo","code":100}
```

#### logging errors

When errors occur, you may want to listen for them in order to provide features such as logging. 
See https://docs.mezzio.dev/mezzio/v3/features/error-handling/#listening-for-errors


```php
use Laminas\Stratigility\Middleware\ErrorHandler;
use Zfegg\PsrMvc\Container\LoggingError\LoggingErrorDelegator;

return [
    'dependencies' => [
        'delegators' => [
            ErrorHandler::class => [
                LoggingErrorDelegator::class,
            ],
        ],
    ],
];
```