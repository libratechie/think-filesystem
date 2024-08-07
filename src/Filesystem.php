<?php

namespace Libratechie\Think;

use think\Facade;

class Filesystem extends Facade
{
    protected static function getFacadeClass(): string
    {
        return FilesystemManager::class;
    }
}