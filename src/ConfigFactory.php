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

namespace Hyperf\TfConfig;

use Dotenv\Repository\RepositoryBuilder;
use Dotenv\Repository\Adapter;
use Psr\Container\ContainerInterface;
use Symfony\Component\Finder\Finder;
use Dotenv\Dotenv;

class ConfigFactory
{
    public function __invoke(ContainerInterface $container)
    {
        // Load env before config.
        if (file_exists(BASE_PATH . '/.env')) {
            $repository = RepositoryBuilder::create()
                ->withReaders([
                    new Adapter\PutenvAdapter(),
                ])
                ->withWriters([
                    new Adapter\PutenvAdapter(),
                ])
                ->immutable()
                ->make();
            Dotenv::create($repository, [BASE_PATH])->load();
        }
        $configPath = BASE_PATH . '/config/';
        $config = $this->readConfig($configPath . 'config.php');
        $autoloadConfig = $this->readPathPhase([BASE_PATH . '/config/autoload']);
        $merged = array_merge_recursive(ProviderConfig::load(), $config, ...$autoloadConfig);
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
        $phase = env('PHASE', ''); //开发环境，如 dev
        $finder->files()->in($paths)->exclude($phase)->name('*.php'); //如果指定了环境变量，就排除环境变量的配置扫描
        foreach ($finder as $file) {
            checkDirSameFile($file->getPath());
            $filePath = $file->getRealPath();
            if ($phase) {//如果有环境变量
                //如绝对路径为 /api/wechat/english/Reading.php 替换为 /dev/api/wechat/english/Reading.php
                $tmpPath = str_replace($file->getRelativePathname(), $phase . DIRECTORY_SEPARATOR . $file->getRelativePathname(), $file->getPathname());
                if (file_exists($tmpPath) && is_readable($tmpPath)) {//如果开发环境中指定了文件路径，就替换读取文件
                    $filePath = $tmpPath;
                }
            }
            $path = $file->getBasename('.php');
            if ($file->getRelativePath()) {
                $path = $file->getRelativePath() . DIRECTORY_SEPARATOR . $file->getBasename('.php');
            }
            $configs[] = pathToArray($path, require $filePath);
        }
        return $configs;
    }
}
