Callable handler decorator
==========================

[![Build Status](https://travis-ci.org/zfegg/callable-handler-decorator.png)](https://travis-ci.org/zfegg/callable-handler-decorator)
[![Coverage Status](https://coveralls.io/repos/github/zfegg/callable-handler-decorator/badge.svg?branch=master)](https://coveralls.io/github/zfegg/callable-handler-decorator?branch=master)
[![Latest Stable Version](https://poser.pugx.org/zfegg/callable-handler-decorator/v/stable.png)](https://packagist.org/packages/zfegg/callable-handler-decorator)


Reflect callback and convert to psr-server-handler decorator. 
Automatically inject parameters into callbacks.


Installation / 安装
-------------------

```bash
composer require zfegg/callable-handler-decorator
```

Usage / 使用
------------

### Example for Mezzio :

```php
// Class file Hello.php

class Hello {
  public function say(
    \Psr\Http\Message\ServerRequestInterface $request, // Inject request param
    string $name, // Inject param from $request->getAttribute('name').
    Foo $foo     //  Inject param from container.
  ) {
    return new TextResponse('hello ' . $name);
  }
}
```

```php

// File config/config.php
// Add ConfigProvider 

new ConfigAggregator([
  Zfegg\CallableHandlerDecorator\ConfigProvider::class,
]);
```

```php

// config/autoload/global.php
// Add demo class factories

use Zfegg\CallableHandlerDecorator\Factory\CallableHandlerFactory;

return [
    'dependencies' => [
        'invokables' => [
            Hello::class,
        ],
        'factories' => [
            Hello::class . '@say' => CallableHandlerFactory::class, 
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

use Zfegg\CallableHandlerDecorator\Factory\CallableHandlerAbstractFactory;

return [
    'dependencies' => [
        'invokables' => [
            Hello::class,
        ],
        'abstract_factories' => [
            CallableHandlerAbstractFactory::class, 
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
