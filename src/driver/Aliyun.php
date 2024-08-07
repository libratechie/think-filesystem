<?php

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
            $this->config['public_url'] = $this->config['bucket'] .'.'. $this->config['endpoint'];
        }
        return new AliyunAdapter($this->config['accessKeyId'], $this->config['accessKeySecret'], $this->config['endpoint'], $this->config['bucket']);
    }

    public function url(string $path): string
    {
        return $this->filesystem->publicUrl($path);
    }
}