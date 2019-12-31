# hyperf-config

`composer require lmz/hyperf-config`

主要的目前是为了加载 `config/autoload` 带文件夹的配置，官方提供的配置文件只读取`.php`配置文件，如果有同名的文件就会被覆盖。

## 使用方法：

在`config/autoload/dependencies.php`添加，其他用法与官方一样
```php
    \Hyperf\Contract\ConfigInterface::class => \Hyperf\TfConfig\ConfigFactory::class,
```

## demo

`config/autoload/demo/test.php` 配置文件

```php
return [
    'config' => [
        'db' => 'test',
    ],
];
```

官方结果：

```php
'test' => [
    'config' => [
        'db' => 'test',
    ],
];
```

本项目结果：

```php
[
    'demo' => [
        'test' => [
            'config' => [
                'db' => 'test',
            ],
        ],
    ],
]
```