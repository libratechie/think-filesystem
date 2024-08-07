<?php

namespace Libratechie\Think;

use InvalidArgumentException;
use think\helper\Arr;
use think\Manager;

/**
 * Filesystem management class
 * Responsible for managing and configuring different file storage drivers
 */
class FilesystemManager extends Manager
{
    // Define namespace for loading different file storage drivers
    protected $namespace = '\\Libratechie\\Think\\driver\\';

    /**
     * Get the specified disk driver
     *
     * @param null|string $name Disk name
     * @return Driver Returns the corresponding driver instance
     */
    public function disk(string $name = null): Driver
    {
        return $this->driver($name);
    }

    /**
     * Resolve the disk type configuration
     *
     * @param string $name Disk name
     * @return string Returns the disk type configuration
     */
    protected function resolveType(string $name): string
    {
        return $this->getDiskConfig($name, 'type', 'local');
    }

    /**
     * Get all configurations of the disk
     *
     * @param string $name Disk name
     * @return mixed Returns the disk configuration
     * @throws InvalidArgumentException Throws exception when the specified disk is not found
     */
    protected function resolveConfig(string $name): mixed
    {
        return $this->getDiskConfig($name);
    }

    /**
     * Get the configuration of the filesystem
     *
     * @param string|null $name Configuration item name
     * @param mixed|null $default Default value
     * @return mixed Returns the corresponding configuration value
     */
    public function getConfig(string $name = null, mixed $default = null): mixed
    {
        if (!is_null($name)) {
            return $this->app->config->get('filesystem.' . $name, $default);
        }

        return $this->app->config->get('filesystem');
    }

    /**
     * Get the configuration of the specified disk
     *
     * @param string $disk Disk name
     * @param null $name Configuration item name
     * @param null $default Default value
     * @return mixed Returns the disk configuration
     * @throws InvalidArgumentException Throws exception when the specified disk is not found
     */
    public function getDiskConfig(string $disk, $name = null, $default = null): mixed
    {
        // Retrieve the configuration for the specified disk
        if ($config = $this->getConfig("disks.$disk")) {
            return Arr::get($config, $name, $default);
        }

        // Throw an exception if the disk configuration is not found
        throw new InvalidArgumentException("Disk [$disk] not found.");
    }

    /**
     * Get the default driver name
     *
     * @return string|null Returns the default driver name
     */
    public function getDefaultDriver(): ?string
    {
        return $this->getConfig('default');
    }
}