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

use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemAdapter;
use League\Flysystem\UnableToSetVisibility;
use League\Flysystem\UnableToWriteFile;
use RuntimeException;
use think\Cache;
use think\File;

abstract class Driver
{
    // Cache instance
    protected ?Cache $cache;

    // Adapter instance
    protected ?FilesystemAdapter $adapter;

    // Filesystem instance
    protected ?Filesystem $filesystem;

    protected array $config = [];

    /**
     * Constructor.
     *
     * @param Cache $cache  Cache instance
     * @param array $config Configuration settings
     */
    public function __construct(Cache $cache, array $config)
    {
        $this->cache = $cache;
        $this->config = array_merge($this->config, $config);

        // Create adapter instance
        $this->adapter = $this->createAdapter();
        // Create filesystem instance
        $this->filesystem = $this->createFilesystem($this->adapter);
    }

    /**
     * Create adapter
     * Subclasses must implement this method to create a specific filesystem adapter.
     */
    abstract protected function createAdapter(): FilesystemAdapter;

    /**
     * Create filesystem.
     *
     * @param FilesystemAdapter $adapter Adapter instance
     *
     * @return Filesystem Returns the filesystem instance
     */
    protected function createFilesystem(FilesystemAdapter $adapter): Filesystem
    {
        // Filter configuration settings, only keep the settings needed by the filesystem
        $config = array_intersect_key($this->config, array_flip(['visibility', 'disable_asserts', 'url', 'public_url']));

        return new Filesystem($adapter, $config);
    }

    /**
     * Get path.
     *
     * @param string $path Path
     *
     * @return string Returns the path
     */
    public function path(string $path): string
    {
        return $path;
    }

    /**
     * Concatenate path and URL.
     *
     * @param string $url  URL
     * @param string $path Path
     *
     * @return string Returns the concatenated URL
     */
    protected function concatPathToUrl(string $url, string $path): string
    {
        return rtrim($url, '/').'/'.ltrim($path, '/');
    }

    /**
     * Get file URL.
     *
     * @param string $path Path
     *
     * @throws RuntimeException Throws exception if the driver does not support retrieving URLs
     *
     * @return string Returns the file URL
     */
    public function url(string $path): string
    {
        throw new RuntimeException('This driver does not support retrieving URLs.');
    }

    /**
     * Store file.
     *
     * @param string $path    Storage path
     * @param File   $file    File instance
     * @param null   $rule    Rule
     * @param array  $options Options
     *
     * @return bool|string Returns the path if storage is successful, otherwise returns false
     */
    public function putFile(string $path, File $file, $rule = null, array $options = []): bool|string
    {
        return $this->putFileAs($path, $file, $file->hashName($rule), $options);
    }

    /**
     * Store file with a specified name.
     *
     * @param string $path    Storage path
     * @param File   $file    File instance
     * @param string $name    File name
     * @param array  $options Options
     *
     * @return bool|string Returns the path if storage is successful, otherwise returns false
     */
    public function putFileAs(string $path, File $file, string $name, array $options = []): bool|string
    {
        // Open file stream
        $stream = fopen($file->getRealPath(), 'r');
        // Generate storage path
        $path = trim($path.'/'.$name, '/');

        // Store file
        $result = $this->put($path, $stream, $options);

        // Close file stream
        if (is_resource($stream)) {
            fclose($stream);
        }

        return $result ? $path : false;
    }

    /**
     * Store file contents.
     *
     * @param string $path     Storage path
     * @param string $contents File contents or file stream
     * @param array  $options  Options
     *
     * @return bool Returns true if successful, otherwise false
     */
    protected function put(string $path, string $contents, array $options = []): bool
    {
        try {
            // Write file stream
            $this->writeStream($path, $contents, $options);
        } catch (UnableToWriteFile|UnableToSetVisibility) {
            return false;
        }

        return true;
    }

    /**
     * Call filesystem method.
     *
     * @param string $method     Method name
     * @param array  $parameters Parameters
     *
     * @return mixed Returns the result of the method call
     */
    public function __call(string $method, array $parameters)
    {
        return $this->filesystem->$method(...$parameters);
    }
}
