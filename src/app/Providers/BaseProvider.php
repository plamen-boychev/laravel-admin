<?php

namespace LAdmin\Providers;

use Exception;
use Illuminate\Support\ServiceProvider;

abstract class BaseProvider extends ServiceProvider
{

    /**
     * Loading the specified file from the configuration directory or from the
     * default configuration - located in __DIR__ . /../../config/*
     * The paths are relative - from that directory => can specify nested files,
     * as preferably this would be done using the DIRECTORY_SEPARATOR contact
     *
     * @param  string $publishedPath - path to the published configuration file starts from [application-path/config]
     * @param  string $defaultPath   - path to the default configuration file - starts from [package-path]/src/app
     *
     * @return void
     */
    protected function loadConfigurationFileOrDefault(string $publishedPath, string $defaultPath = null)
    {
        $defaultPath = $defaultPath ?? $publishedPath;
        $filepath = null;

        if (($filepath = $this->getApplicationConfigFilePath($publishedPath)) !== null) {
            require_once $filepath;
            return;
        } else if (($filepath = $this->getPackageConfigFilePath($publishedPath)) !== null) {
            require_once $filepath;
            return;
        }

        throw new Exception('Configuration for static routes could not be loaded properly!');
    }

    /**
     * Returns the path to the specified configuration file, processed for proper file loading:
     *  - adds the configuration files extension
     *
     * @param  string $relativePath - path to the application configuration file starts from [application-path/config]
     *
     * @return string|null
     */
    protected function normalizePathToConfigFile(string $relativePath)
    {
        # Adding the configuration file extension
        $ext = 'php';
        $relativePath = "{$relativePath}.{$ext}";

        /**
         * @todo Other normalization tasks
         */

        return $relativePath;
    }

    /**
     * Returns the path to the specified configuration file, located in the application's configuration directory
     *
     * @param  string $relativePath - path to the application configuration file starts from [application-path/config]
     *
     * @return string|null
     */
    protected function getApplicationConfigFilePath(string $relativePath)
    {
        $relativePath = $this->normalizePathToConfigFile($relativePath);
        $appFilePath  = config_path($relativePath);
        $exists = file_exists($appFilePath);

        if ($exists === true) {
            return realpath($appFilePath);
        }

        return null;
    }

    /**
     * Returns the path to the specified configuration file, located in the package's configuration directory
     *
     * @param  string $relativePath - path to the application configuration file starts from [package-path/src/config]
     *
     * @return string|null
     */
    protected function getPackageConfigFilePath(string $relativePath)
    {
        $relativePath = $this->normalizePathToConfigFile($relativePath);
        $appFilePath  = $this->configPath($relativePath);
        $exists = file_exists($appFilePath);

        if ($exists === true) {
            return realpath($appFilePath);
        }

        return null;
    }

    /**
     * Requires the specified file, relative to the base path of the package
     *
     * @param  string $relativePath
     *
     * @return integer
     */
    protected function requireFile(string $relativePath)
    {
        return require_once $this->basePath($this->normalizePathToConfigFile($relativePath));
    }

    /**
     * Includes the specified file, relative to the base path of the package
     *
     * @param  string $relativePath
     *
     * @return integer
     */
    protected function includeFile(string $relativePath)
    {
        return include $this->basePath($this->normalizePathToConfigFile($relativePath));
    }

    /**
     * Includes once the specified file, relative to the base path of the package
     *
     * @param  string $relativePath
     *
     * @return integer
     */
    protected function includeFileOnce(string $relativePath)
    {
        return include_once $this->basePath($this->normalizePathToConfigFile($relativePath));
    }

    /**
     * Returns the passed relative path concatinated to the default package's configuration direcotry
     *
     * @param  string $relativePath
     *
     * @return string string
     */
    protected function configPath(string $relativePath)
    {
        $ds = $this->directorySeparator();
        $configDirectory = 'config'
            . $ds . $relativePath
        ;

        return realpath($this->basePath($relativePath));
    }

    /**
     * Returns the directory separator for the filesystem
     *
     * @return string
     */
    protected function directorySeparator()
    {
        return DIRECTORY_SEPARATOR;
    }

    protected function basePath(string $relativePath)
    {
        $ds = $this->directorySeparator();
        $baseDirectory = __DIR__
            . $ds . '..'
            . $ds . '..'
        ;

        return realpath($baseDirectory . $ds . $relativePath);
    }

}
