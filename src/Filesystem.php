<?php

/*
 * This file is part of the libratechie/think-filesystem.
 *
 * (c) libratechie <libratechie@foxmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Libratechie\Think;

use think\Facade;

class Filesystem extends Facade
{
    protected static function getFacadeClass(): string
    {
        return FilesystemManager::class;
    }
}
