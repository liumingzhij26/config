<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://doc.hyperf.io
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace Hyperf\TfConfig;

use Dotenv\Dotenv;
use Psr\Container\ContainerInterface;
use Symfony\Component\Finder\Finder;

class ConfigFactory
{
    public function __invoke(ContainerInterface $container)
    {
        // Load env before config.
        if (file_exists(BASE_PATH . '/.env')) {
            Dotenv::create([BASE_PATH])->load();
        }
        $configPath = BASE_PATH . '/config/';
        $config = $this->readConfig($configPath . 'config.php');
        $serverConfig = $this->readConfig($configPath . 'server.php');
        $autoloadConfig = $this->readPathPhase([BASE_PATH . '/config/autoload']);
        $merged = array_merge_recursive(ProviderConfig::load(), $serverConfig, $config, ...$autoloadConfig);
        return new Config($merged);
    }

    private function readConfig(string $configPath): array
    {
        $config = [];
        if (file_exists($configPath) && is_readable($configPath)) {
            $config = require $configPath;
        }
        return is_array($config) ? $config : [];
    }

    private function readPaths(array $paths)
    {
        $configs = [];
        $finder = new Finder();
        $finder->files()->in($paths)->name('*.php');
        foreach ($finder as $file) {
            $configs[] = [
                $file->getBasename('.php') => require $file->getRealPath(),
            ];
        }
        return $configs;
    }

    /**
     * 读取多个目录下的 php 文件.
     *
     * api/wechat/english/Reading.php => dev/api/wechat/english/Reading.php
     *
     * @param array $paths
     *
     * @return array
     */
    private function readPathPhase(array $paths)
    {
        $configs = [];
        $finder = new Finder();
        $phase = env('PHASE'); //开发环境，如 dev
        $finder->files()->in($paths)->exclude($phase)->name('*.php'); //如果指定了环境变量，就排除环境变量的配置扫描
        foreach ($finder as $file) {
            checkDirSameFile($file->getPath());
            $filePath = $file->getRealPath();
            if ($phase) {
                $tmpPath = str_replace($file->getRelativePathname(), $phase . DIRECTORY_SEPARATOR . $file->getRelativePathname(), $file->getPathname());
                if (file_exists($tmpPath) && is_readable($tmpPath)) {//如果开发环境中指定了文件路径，就替换读取文件
                    $filePath = $tmpPath;
                }
            }
            $configs[] = pathToArray(rtrim($file->getRelativePathname(), '.php'), require $filePath);
        }
        return $configs;
    }
}
