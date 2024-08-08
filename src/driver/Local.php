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
use League\Flysystem\Local\LocalFilesystemAdapter;
use League\Flysystem\PathNormalizer;
use League\Flysystem\PathPrefixer;
use League\Flysystem\UnixVisibility\PortableVisibilityConverter;
use League\Flysystem\Visibility;
use League\Flysystem\WhitespacePathNormalizer;
use Libratechie\Think\Driver;

class Local extends Driver
{
    // Configuration information, default includes the root directory
    protected array $config = [
        'root' => '',
    ];

    /**
     * @var PathPrefixer|null Path prefix handler
     */
    protected ?PathPrefixer $prefixer = null;

    /**
     * @var PathNormalizer|null Path normalization handler
     */
    protected ?PathNormalizer $normalizer = null;

    /**
     * Create a local filesystem adapter.
     *
     * @return FilesystemAdapter Returns the filesystem adapter instance
     */
    protected function createAdapter(): FilesystemAdapter
    {
        // Set file permissions and visibility
        $visibility = PortableVisibilityConverter::fromArray(
            $this->config['permissions'] ?? [],
            $this->config['visibility'] ?? Visibility::PRIVATE
        );

        // Set symlink handling
        $links = ($this->config['links'] ?? null) === 'skip'
            ? LocalFilesystemAdapter::SKIP_LINKS
            : LocalFilesystemAdapter::DISALLOW_LINKS;

        // Create and return the local filesystem adapter
        return new LocalFilesystemAdapter(
            $this->config['root'],
            $visibility,
            $this->config['lock'] ?? LOCK_EX,
            $links
        );
    }

    /**
     * Get the path prefix handler instance.
     *
     * @return PathPrefixer Returns the path prefix handler instance
     */
    protected function prefixer(): PathPrefixer
    {
        if (!$this->prefixer) {
            $this->prefixer = new PathPrefixer($this->config['root'], DIRECTORY_SEPARATOR);
        }

        return $this->prefixer;
    }

    /**
     * Get the path normalization handler instance.
     *
     * @return WhitespacePathNormalizer Returns the path normalization handler instance
     */
    protected function normalizer(): WhitespacePathNormalizer
    {
        if (!$this->normalizer) {
            $this->normalizer = new WhitespacePathNormalizer();
        }

        return $this->normalizer;
    }

    /**
     * Get the file access URL.
     *
     * @param string $path File path
     *
     * @return string Returns the file access URL
     */
    public function url(string $path): string
    {
        // Normalize the path
        $path = $this->normalizer()->normalizePath($path);
        // If URL is configured, concatenate it with the path and return
        if (isset($this->config['url'])) {
            return $this->concatPathToUrl($this->config['url'], $path);
        }

        // Otherwise, call the parent method
        return parent::url($path);
    }

    /**
     * Get the full file path.
     *
     * @param string $path File path
     *
     * @return string Returns the full file path
     */
    public function path(string $path): string
    {
        return $this->prefixer()->prefixPath($path);
    }
}
