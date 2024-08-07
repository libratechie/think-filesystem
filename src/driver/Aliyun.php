<?php

/*
 * This file is part of the libratechie/think-filesystem.
 *
 * (c) libratechie <libratechie@foxmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Libratechie\Think\driver;

use Libratechie\Think\Driver;
use League\Flysystem\FilesystemAdapter;
use Libratechie\Flysystem\Aliyun\AliyunAdapter;

class Aliyun extends Driver
{
    protected function createAdapter(): FilesystemAdapter
    {
        if (isset($this->config['domain'])) {
            $this->config['public_url'] = $this->config['domain'];
        } else {
            $this->config['public_url'] = $this->config['bucket'].'.'.$this->config['endpoint'];
        }

        return new AliyunAdapter($this->config['accessKeyId'], $this->config['accessKeySecret'], $this->config['endpoint'], $this->config['bucket']);
    }

    public function url(string $path): string
    {
        return $this->filesystem->publicUrl($path);
    }
}
