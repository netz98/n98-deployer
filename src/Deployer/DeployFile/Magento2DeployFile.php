<?php
/**
 * @copyright Copyright (c) 2017 netz98 new media GmbH (http://www.netz98.de)
 *
 * @see LICENSE
 */

namespace N98\Deployer\DeployFile;

use N98\Deployer\Config\ReleaseConfig;
use N98\Deployer\Task\BuildTasks;
use N98\Deployer\Task\CleanupTasks;
use N98\Deployer\Task\DeployTasks;
use N98\Deployer\Task\MagentoTasks;
use N98\Deployer\Task\SystemTasks;

/**
 * Magento2DeployFile
 */
class Magento2DeployFile
{
    public static function configuration()
    {
        $sharedFiles = [
            'src/app/etc/env.php',
        ];
        \Deployer\set('shared_files', $sharedFiles);

        $sharedDirs = [
            'pub/media',
            'var/log',
            'var/session',
            'var/composer_home',
        ];
        \Deployer\set('shared_dirs', $sharedDirs);

        $writeDirs = [
            "var",
            "pub/static'",
            "pub/media'",
        ];
        \Deployer\set('writable_dirs', $writeDirs);

        $artifacts = [
            'shop.tar.gz',
            'pub_static.tar.gz',
            'var_generation.tar.gz',
            'var_di.tar.gz',
        ];
        \Deployer\set('magento_build_artifacts', $artifacts);
    }

    public static function tasks()
    {
        ReleaseConfig::register();
        DeployTasks::register();
        BuildTasks::register();
        MagentoTasks::register();
        SystemTasks::register();
        CleanupTasks::register();
    }
}