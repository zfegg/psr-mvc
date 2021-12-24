PSR MVC handler
================

[![GitHub Actions: Run tests](https://github.com/zfegg/psr-mvc/workflows/qa/badge.svg)](https://github.com/zfegg/psr-mvc/actions?query=workflow%3A%22qa%22)
[![Build Status](https://travis-ci.org/zfegg/psr-mvc.png)](https://travis-ci.org/zfegg/psr-mvc)
[![Coverage Status](https://coveralls.io/repos/github/zfegg/psr-mvc/badge.svg?branch=master)](https://coveralls.io/github/zfegg/psr-mvc?branch=master)
[![Latest Stable Version](https://poser.pugx.org/zfegg/psr-mvc/v/stable.png)](https://packagist.org/packages/zfegg/psr-mvc)


Reflect callback and convert to psr-server-handler decorator. 
Automatically inject parameters into callbacks.


Installation / 安装
-------------------

```bash
composer require zfegg/psr-mvc
```

Usage / 使用
------------

Attributes usage like [dotnet core MVC](https://docs.microsoft.com/en-us/aspnet/core/mvc/controllers/routing?view=aspnetcore-6.0)

### MVC Route

#### Attributes

- `Route(string $path, array $middlewares = [], ?string $name = null, array $options = [], ?array $methods = null)`
- `HttpGet(string $path, array $middlewares = [], ?string $name = null, array $options = [])`
- `HttpPost(string $path, array $middlewares = [], ?string $name = null, array $options = [])`
- `HttpPatch(string $path, array $middlewares = [], ?string $name = null, array $options = [])`
- `HttpPut(string $path, array $middlewares = [], ?string $name = null, array $options = [])`
- `HttpDelete(string $path, array $middlewares = [], ?string $name = null, array $options = [])`
- `HttpHead(string $path, array $middlewares = [], ?string $name = null, array $options = [])`

Register routes with attributes.

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

### Callback middleware.

Callback middleware for resolve response.

#### `...\Middleware\Serializer`

For resolve callback result to ResponseInterface.

```php

class ExampleResponseController {
    #[HttpPost('/hello')]  // `void` -> HTTP 204 No Content
    public function hello(): void {
    }
    
    #[HttpPost('/hello')]
    public function hello(): array {
     return [];
    }
    // Will use `symfony/serializer` for serialize result.
    // Get format from FormatMatcher.
    // `$serializer->serialize($result, 'json')`
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

Register route.

```php
// routes.php

(function ($app) {
 // `$container->get("Hello@say")` is a class implements RequestHandlerInterface.
 $app->get('/hello/{name}', Hello::class . '@say'); 
});
```

### CallableHandlerAbstractFactory

Require `laminas/laminias-servicemanager`.

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
