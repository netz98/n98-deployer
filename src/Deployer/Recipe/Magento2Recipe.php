<?php
/**
 * @copyright Copyright (c) 2017 netz98 new media GmbH (http://www.netz98.de)
 *
 * @see LICENSE
 */

namespace N98\Deployer\Recipe;

use N98\Deployer\Config\ReleaseConfig;
use N98\Deployer\Task\BuildTasks;
use N98\Deployer\Task\CleanupTasks;
use N98\Deployer\Task\DeployTasks;
use N98\Deployer\Task\MagentoTasks;
use N98\Deployer\Task\SystemTasks;

/**
 * Magento2DeployFile
 */
class Magento2Recipe
{
    public static function configuration()
    {
        $appDir = '';

        \Deployer\set('app_dir', $appDir);

        $sharedFiles = [
            'app/etc/env.php',
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

        $chownDirs = [
            // Change File ownership to pub/static, it has to be writable even in production mode as there is a test
            'release_pub_static' => [
                'dir' => "{{release_path_app}}/pub/static",
                'mode' => 'g+w',
                'owner' => "{{webserver_user}}:{{webserver_group}}",
            ],
            // Change file ownership and acl in var dir (not all dirs under var are linked)
            'release_var' => [
                'dir' => "{{release_path_app}}/var",
                'mode' => '775',
                'owner' => "{{webserver_user}}:{{webserver_group}}",
            ],
            // Change file ownership and acl in shared directly (in case chmod has no -H option)
            'shared_var' => [
                'dir' => "{{shared_path_app}}/var",
                'mode' => '775',
                'owner' => "{{webserver_user}}:{{webserver_group}}",
            ],
            'shared_pub_media' => [
                'dir' => "{{shared_path_app}}/pub/media",
                'mode' => '775',
                'owner' => "{{webserver_user}}:{{webserver_group}}",
            ],
        ];
        \Deployer\set('change_owner_mode_dirs', $chownDirs);

        \Deployer\set('artifacts_dir', 'artifacts');

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