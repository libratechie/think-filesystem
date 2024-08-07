<?php

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