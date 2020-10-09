<?php

declare(strict_types=1);

/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
use Hyperf\Contract\ConfigInterface;
use Hyperf\Utils\ApplicationContext;

if (!function_exists('config')) {
    function config(string $key, $default = null)
    {
        if (!ApplicationContext::hasContainer()) {
            throw new \RuntimeException('The application context lacks the container.');
        }
        $container = ApplicationContext::getContainer();
        if (!$container->has(ConfigInterface::class)) {
            throw new \RuntimeException('ConfigInterface is missing in container.');
        }
        return $container->get(ConfigInterface::class)->get($key, $default);
    }
}

/**
 * 文件路径转多维数据.
 *
 * a/b/c ['a']['b']['c']
 *
 * @param string $path
 * @param array $value
 *
 * @return array
 */
if (!function_exists('pathToArray')) {
    function pathToArray($path, array $value): array
    {
        $config = [];
        if (!$value) {
            return $config;
        }

        $tmp = &$config;
        foreach (explode(DIRECTORY_SEPARATOR, $path) as $key) {
            $tmp[$key] = [];
            $tmp = &$tmp[$key];
        }
        $tmp = $value;

        return $config;
    }
}

/**
 * 判断文件夹是否有同名文件
 *
 * @param $dir
 */
if (!function_exists('checkDirSameFile')) {
    function checkDirSameFile($dir)
    {
        $filename = sprintf($dir . '%s', '.php');
        if (file_exists($filename) && is_readable($filename)) {
            throw new \RuntimeException(sprintf('Cannot have the same name as the folder %s', $filename));
        }
    }
}
