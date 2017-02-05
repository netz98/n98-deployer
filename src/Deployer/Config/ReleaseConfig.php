<?php
/**
 * @copyright Copyright (c) 2017 netz98 GmbH (http://www.netz98.de)
 *
 * @see LICENSE
 */

namespace N98\Deployer\Config;

use Deployer\Task\Context as TaskContext;
use N98\Deployer\Service\GetReleasesListService;
use N98\Deployer\Service\GetReleasesNameService;

/**
 * ReleaseConfig
 */
class ReleaseConfig
{
    /**
     * Register Config Proxies that are executed when config is fetched the first time
     */
    public static function register()
    {
        \Deployer\set('release_path_app', function () { return ReleaseConfig::getReleasePathAppDir(); });
        \Deployer\set('shared_path_app', function () { return ReleaseConfig::getSharedPathAppDir(); });

        \Deployer\set('release_name', function () { return GetReleasesNameService::execute(); });
        \Deployer\set('releases_list', function () { return GetReleasesListService::execute(); });
    }

    /**
     * @return string
     */
    public static function getReleasePathAppDir()
    {
        return self::buildAppPath('{{release_path}}');
    }

    /**
     * @return string
     */
    public static function getSharedPathAppDir()
    {
        return self::buildAppPath('{{deploy_path}}/shared');
    }

    /**
     * Get AppDir
     *
     * @return string
     */
    public static function getAppDir()
    {
        return \Deployer\get('app_dir');
    }

    /**
     * @param string $appPath
     *
     * @return string
     */
    protected static function buildAppPath($appPath)
    {
        $appdir = self::getAppDir();
        if (!empty($appdir)) {
            $appPath .= "/$appdir";
        }

        return $appPath;
    }

    /**
     * Set the Release List Proxy to Env in case the next call should read the releases from scratch
     */
    public static function setReleaseListProxyToEnv()
    {
        // Reset so it will be loaded
        $env = TaskContext::get()->getEnvironment();
        $env->set('releases_list', function () { return ReleaseConfig::getReleasesList(); });
    }
}