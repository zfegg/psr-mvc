[English](README.md) | 简体中文

PSR MVC 处理器
================

[![GitHub Actions: Run tests](https://github.com/zfegg/psr-mvc/workflows/qa/badge.svg)](https://github.com/zfegg/psr-mvc/actions?query=workflow%3A%22qa%22)
[![Coverage Status](https://coveralls.io/repos/github/zfegg/psr-mvc/badge.svg?branch=main)](https://coveralls.io/github/zfegg/psr-mvc?branch=main)
[![Latest Stable Version](https://poser.pugx.org/zfegg/psr-mvc/v/stable.png)](https://packagist.org/packages/zfegg/psr-mvc)

在PSR处理器的应用程序中使用MVC风格, 类似 [dotnet core MVC](https://docs.microsoft.com/en-us/aspnet/core/mvc/controllers/routing?view=aspnetcore-6.0).  
使用PHP属性(注解), 将控制器转换成 PSR15 `RequestHandlerInterface` 对象.

安装
---

```bash
composer require zfegg/psr-mvc
```

使用
---

属性的使用类似 [dotnet core MVC](https://docs.microsoft.com/en-us/aspnet/core/mvc/controllers/routing?view=aspnetcore-6.0)

### MVC 路由

#### 开始在 Mezzio 中使用

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
use Zfegg\PsrMvc\Routing\RouteMetadata;

return [
    // 添加扫描控制器目录
    RouteMetadata::class => [
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

#### PHP 8 属性

支持的方法属性列表

- `Route(string $path, array $middlewares = [], ?string $name = null, array $options = [], ?array $methods = null)`
- `HttpGet(string $path, array $middlewares = [], ?string $name = null, array $options = [])`
- `HttpPost(string $path, array $middlewares = [], ?string $name = null, array $options = [])`
- `HttpPatch(string $path, array $middlewares = [], ?string $name = null, array $options = [])`
- `HttpPut(string $path, array $middlewares = [], ?string $name = null, array $options = [])`
- `HttpDelete(string $path, array $middlewares = [], ?string $name = null, array $options = [])`
- `HttpHead(string $path, array $middlewares = [], ?string $name = null, array $options = [])`


#### 路由结合

使用 `[controller]`, `[action]` 自动识别控制器和动作

```php
#[Route("/[controller]/[action]")]
public class HomeController
{
    #[HttpGet]  // GET /home/index
    public index(){}
}
```

Restful 风格示例

```php
use Psr\Http\Message\ResponseInterface;

#[Route("/api/[controller]")] // 路由前缀 `/api/products`
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


### 包装控制器处理程序

#### 使用参数属性

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

#### 默认参数绑定

```php

class ExampleController {
    #[HttpPost('/api/[controller]/[action]/{id}')] 
    public function hello(
       ServerRequestInterface $request,  // 默认绑定请求来源的 `ServerRequestInterface` 对象.
       int $id,        // 默认绑定 `$request->getAttribute('id')`.
       Foo $fooName,   // 如果IoC容器中存在 `Foo`, 默认取容器中的对象, 否则从获取.
                       // ```
                       // $value = $request->getAttribute(Foo::class, $request->getAttribute('fooName'));
                       // $value = $value === null && $container->has(Foo::class) ? container->get(Foo::class) : $value;
                       // ```
    ): void {}
}
```

### 返回结果预处理

解决各种类型的方法结果转换为 'Psr\Http\Message\ResponseInterface'.

#### `Zfegg\PsrMvc\Preparer\DefaultPreparer`

默认的结果预处理器

```php
class ExampleResponseController {
    #[HttpPost('/hello-void')]  // `void` -> HTTP 204 No Content
    public function helloVoid(): void {
    }

    /*
     *  结果类型是字符串, 默认返回 `HtmlResponse` 对象
     *  `new HtmlResponse($result)`
     */
    #[HttpPost('/hello-string')]
    public function helloString(): string {
      return '<h1>Hello</h1>';
    }
    
    /*
     *  默认返回 `JsonResponse` 对象
     *  `new JsonResponse($result)`
     */
    #[HttpPost('/hello-array')]
    public function helloArray(): array {
      return ['foo' => 'a', 'bar' => 'b'];
    }
}
```

#### `Zfegg\PsrMvc\Preparer\SerializationPreparer` (推荐使用)

将使用 `symfony/serializer` 序列化结果。

```php
class ExampleResponseController {
    #[HttpPost('/hello-void')]  // `void` -> HTTP 204 No Content
    public function helloVoid(): void {
    }

    /*
     * 使用 `symfony/serializer` 组件对结果序列化处理.
     * `FormatMatcher` 可匹配相应的序列化格式 `$format`.
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

支持的预处理器选项:

| 键 | 说明 |
|---|-----|
| status | 响应状态码 |
| headers | 响应头 | 
| `<more>` | `$serializer->serialize` context 变量 |

#### `#[PrepareResult]` 属性

使用`#[PrepareResult]` 选择需要的预处理器和传递预处理参数

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

### 手动注册路由

使用 `CallableHandlerAbstractFactory` 手动注册路由

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

// 手动注册
$app->get('/foo-method', ExampleController::class . '@fooMethod')
```
