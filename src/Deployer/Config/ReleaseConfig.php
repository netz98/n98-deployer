<?php
/**
 * @copyright Copyright (c) 2017 netz98 GmbH (http://www.netz98.de)
 *
 * @see LICENSE
 */

namespace N98\Deployer\Config;

use Deployer\Task\Context as TaskContext;
use Deployer\Type\Csv as CsvType;

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

        \Deployer\set('release_name', function () { return ReleaseConfig::getReleaseName(); });
        \Deployer\set('releases_list', function () { return ReleaseConfig::getReleasesList(); });
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
     * Determine the release name by branch or tag, otherwise uses datetime string
     *
     * this method is an overwrite of the Deployer release-name logic
     *
     * @return string
     */
    public static function getReleaseName()
    {
        $release = null;

        // Get release-name from branch
        $input = \Deployer\input();
        if ($input->hasOption('branch')) {
            $branch = $input->getOption('branch');
            if (!empty($branch)) {
                $release = $branch;
            }
        }

        if ($release !== null) {
            return $release;
        }

        // Get release-name from tag
        $input = \Deployer\input();
        if ($input->hasOption('tag')) {
            $tag = $input->getOption('tag');
            if (!empty($tag)) {
                $release = $tag;
            }
        }

        if ($release !== null) {
            return $release;
        }

        $release = date('Ymdhis');

        return $release;
    }

    /**
     * Returns a list of releases on server.
     *
     * @return array
     */
    public static function getReleasesList()
    {
        \Deployer\cd('{{deploy_path}}');

        // If there is no releases return empty list.
        $cmdReleaseDirs = '[ -d releases ] && [ "$(ls -A releases)" ] && echo "true" || echo "false"';
        $hasReleaseDirs = \Deployer\run($cmdReleaseDirs)->toBool();
        if (!$hasReleaseDirs) {
            return [];
        }

        // Will list only dirs in releases.
        $list = \Deployer\run('cd releases && ls -t -d */')->toArray();

        // Prepare list.
        $list = array_map(function ($release) { return basename(rtrim($release, '/')); }, $list);

        $releases = []; // Releases list.

        // Collect releases based on .dep/releases info.
        // Other will be ignored.

        $hasReleasesList = \Deployer\run('if [ -f .dep/releases ]; then echo "true"; fi')->toBool();
        if (!$hasReleasesList) {
            return $releases;
        }

        // we do not filter the keep_releases here, as we want a full list
        $csv = \Deployer\run('cat .dep/releases');

        $metainfo = CsvType::parse($csv);

        for ($i = count($metainfo) - 1; $i >= 0; --$i) {
            if (is_array($metainfo[$i]) && count($metainfo[$i]) >= 2) {
                list($date, $release) = $metainfo[$i];
                $index = array_search($release, $list, true);
                if ($index !== false) {
                    $releases[] = $release;
                    unset($list[$index]);
                }
            }
        }

        return $releases;
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