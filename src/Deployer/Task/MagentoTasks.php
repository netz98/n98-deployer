<?php
/**
 * @copyright Copyright (c) 2017 netz98 GmbH (http://www.netz98.de)
 *
 * @see LICENSE
 */

namespace N98\Deployer\Task;

use N98\Deployer\Registry as Deployer;

/**
 * MagentoTasks
 */
class MagentoTasks extends TaskAbstract
{
    const TASK_MAINTENANCE_MODE_ENABLE = 'magento:maintenance_mode_enable';
    const TASK_MAINTENANCE_MODE_DISABLE = 'magento:maintenance_mode_disable';
    const TASK_SYMLINKS_ENABLE = 'magento:symlinks_enable';
    const TASK_SETUP_UPGRADE = 'magento:setup_upgrade';
    const TASK_SETUP_DOWNGRADE = 'magento:setup_downgrade';
    const TASK_CONFIG_DATA_EXPORT = 'magento:config_data_export';
    const TASK_CONFIG_DATA_IMPORT = 'magento:config_data_import';
    const TASK_CMS_DATA_IMPORT = 'magento:cms_data_import';
    const TASK_CACHE_ENABLE = 'magento:cache_enable';
    const TASK_CACHE_DISABLE = 'magento:cache_disable';
    const TASK_CACHE_CLEAR = 'magento:cache_clear';

    public static function register()
    {
        Deployer::task(
            self::TASK_MAINTENANCE_MODE_ENABLE, 'Enable maintenance mode',
            function () { MagentoTasks::toggleMaintenenceMode(true); }
        );
        Deployer::task(
            self::TASK_MAINTENANCE_MODE_DISABLE, 'Disable maintenance mode',
            function () { MagentoTasks::toggleMaintenenceMode(false); }
        );
        Deployer::task(
            self::TASK_SYMLINKS_ENABLE, 'Allow symlinks',
            function () { MagentoTasks::allowSymlinks(); }
        );
        Deployer::task(
            MagentoTasks::TASK_SETUP_UPGRADE, 'run Magento Updates',
            function () { MagentoTasks::runSetupUpgrade(); }, ['db']
        );
        Deployer::task(
            MagentoTasks::TASK_SETUP_DOWNGRADE, 'run Magento Downgrade',
            function () { MagentoTasks::runSetupDowngrade(); },
            ['db']
        );
        Deployer::task(
            MagentoTasks::TASK_CONFIG_DATA_EXPORT, 'Magento config backup',
            function () { MagentoTasks::backupMagentoConfig(); },
            ['db']
        );
        Deployer::task(
            MagentoTasks::TASK_CONFIG_DATA_IMPORT, 'Magento config update',
            function () { MagentoTasks::updateMagentoConfig(); },
            ['db']
        );
        Deployer::task(
            MagentoTasks::TASK_CMS_DATA_IMPORT, 'Magento CMS import',
            function () { MagentoTasks::importCmsData(); },
            ['db']
        );
        Deployer::task(
            MagentoTasks::TASK_CACHE_ENABLE, 'Enable Magento Cache',
            function () { MagentoTasks::activateMagentoCache(true); },
            ['db']
        );
        Deployer::task(
            MagentoTasks::TASK_CACHE_DISABLE, 'Disable Magento Cache',
            function () { MagentoTasks::activateMagentoCache(false); },
            ['db']
        );
        Deployer::task(
            MagentoTasks::TASK_CACHE_CLEAR, 'Clear Magento Cache',
            function () { MagentoTasks::flushMagentoCache(); },
            ['db']
        );
    }

    /**
     * Toggle Maintenence Mode
     *
     * @param bool $enabled
     */
    public static function toggleMaintenenceMode($enabled)
    {
        $maintenance = $enabled === true ? 'maintenance:enable' : 'maintenance:disable';

        \Deployer\cd('{{release_path_app}}');
        \Deployer\run("php bin/magento $maintenance");
    }

    /**
     * Allow Symlinks
     */
    public static function allowSymlinks()
    {
        $binMagerun = self::getBinMagerun2();

        \Deployer\cd('{{release_path_app}}');
        \Deployer\run("$binMagerun dev:symlinks  --global --on");
    }

    /**
     * Run Magento setup:upgrade
     */
    public static function runSetupUpgrade()
    {
        \Deployer\cd('{{release_path_app}}');
        \Deployer\run("php bin/magento setup:upgrade --keep-generated");
    }

    /**
     * Run Magento setup:upgrade
     */
    public static function runSetupDowngrade()
    {
        $binMagerun = self::getBinMagerun2();

        \Deployer\cd('{{release_path_app}}');
        \Deployer\run("$binMagerun sys:setup:downgrade-versions");
    }

    /**
     * Import the Magento Config using the config data files
     *
     * config:
     *  - config_store_env
     *
     * requirements:
     *  - config:data:import command through semaio/Magento2-ConfigImportExport
     *  - <git-repo>/config/store/<config_store_env> directory tree
     *
     * @throws \Symfony\Component\Console\Exception\InvalidArgumentException
     */
    public static function updateMagentoConfig()
    {
        $env = \Deployer\get('config_store_env');
        if (empty($env)) {
            $env = \Deployer\input()->getArgument('stage');
        }

        $dir = \Deployer\get('config_store_dir');
        if (empty($dir)) {
            $dir = '{{release_path}}/config/store';
        }

        \Deployer\cd('{{release_path_app}}');
        \Deployer\run("php bin/magento config:data:import $dir $env");
    }

    /**
     * Export the Magento Config using the config data files
     * Uses var directory and a date/time prefixed subdirectory.
     * e.g. {{release_path_app/var/20170908_111421_config_backup/}}
     *
     * Shoud be ran BEFORE config import, to have a backup of Magento config.
     *
     * requirements:
     *  - config:data:export command through semaio/Magento2-ConfigImportExport
     *
     * @throws \Symfony\Component\Console\Exception\InvalidArgumentException
     */
    public static function backupMagentoConfig()
    {
        \Deployer\cd('{{release_path_app}}');
        \Deployer\run("php bin/magento config:data:export --filePerNameSpace=y --format=yaml --filename=config_backup/");
    }

    /**
     * Import CMS data
     */
    public static function importCmsData()
    {
        \Deployer\cd('{{release_path_app}}');
        \Deployer\run("php bin/magento cms:import");
    }

    /**
     * Enable or Disable MagentoCache
     *
     * @param $enabled
     */
    public static function activateMagentoCache($enabled)
    {
        $cache = $enabled === true ? 'cache:enable' : 'cache:disable';

        \Deployer\cd('{{release_path_app}}');
        \Deployer\run("php bin/magento $cache");
    }

    /**
     * Flush Magento cache
     */
    public static function flushMagentoCache()
    {
        \Deployer\cd('{{release_path_app}}');
        \Deployer\run("php bin/magento cache:flush");
    }

    /**
     * @return string
     */
    protected static function getBinMagerun2()
    {
        return \Deployer\get('bin/n98_magerun2');
    }

}