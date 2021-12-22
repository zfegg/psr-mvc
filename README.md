Callable handler decorator
==========================

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
