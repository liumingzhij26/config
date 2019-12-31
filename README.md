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

## 使用环境变量

根目录下有`.env`文件

```ini
PHASE=dev
```

`PHASE` 可以自定义如`dev` `publish` `production` `rd` 等

如 `PHASE=dev` ，`config/autoload` 下面有一个 `database.php` ，可以新建 `config/autoload/` 新建`dev/database.php`文件夹，系统会优先读取 `config/autoload/dev/database.php` 配置。



## 项目目的
### 1、为什么不使用`.env`解决问题
就拿数据库配置来说，目前我们有`5个数据库集群`，使用`.env`管理，就只能写成`env('DB_HOST_1', '127.0.0.1')` `env('DB_HOST_2', '127.0.0.1')`等，都写在`.env`里面会非常多配置

### 2、为什么不使用配置系统
新项目目前没有`Apollo` `阿里云 ACM 应用配置管理`，开发环境调试比较麻烦

## 注意事项
1. 应该在 `.gitignore` 里面加上 `config/autoload/dev/*` 目录，不允许提交到代码库上面
2. 不允许子文件夹名与`PHASE`值同名，不会生效，如`config/autoload/demo/dev/test.php`，`dev` 文件夹不会被读取
