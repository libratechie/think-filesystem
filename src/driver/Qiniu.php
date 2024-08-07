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

use League\Flysystem\FilesystemAdapter;
use Overtrue\Flysystem\Qiniu\QiniuAdapter;
use Libratechie\Think\Driver;

class Qiniu extends Driver
{
    protected function createAdapter(): FilesystemAdapter
    {
        return new QiniuAdapter($this->config['accessKey'], $this->config['secretKey'], $this->config['bucket'], $this->config['domain']);
    }

    public function url(string $path): string
    {
        return $this->adapter->getUrl($path);
    }
}
