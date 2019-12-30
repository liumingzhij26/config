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

use Hyperf\Config\Listener\RegisterPropertyHandlerListener;
use Hyperf\Contract\ConfigInterface;

class ConfigProvider
{
    public function __invoke(): array
    {
        print_r([__FILE__]);
        return [
            'dependencies' => [
                ConfigInterface::class => ConfigFactory::class,
            ],
            'listeners' => [
                RegisterPropertyHandlerListener::class,
            ],
            'annotations' => [
                'scan' => [
                    'paths' => [
                        __DIR__,
                    ],
                ],
            ],
        ];
    }
}
