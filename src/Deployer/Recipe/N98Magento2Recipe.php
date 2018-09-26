<?php
/**
 * @copyright Copyright (c) 2017 netz98 new media GmbH (http://www.netz98.de)
 *
 * @see LICENSE
 */

namespace N98\Deployer\Recipe;

/**
 * N98Magento2DeployFile
 */
class N98Magento2Recipe extends Magento2Recipe
{
    public static function configuration()
    {
        parent::configuration();

        \Deployer\set('app_dir', 'src/');

        $sharedFiles = [
            "{{app_dir}}app/etc/env.php",
        ];
        \Deployer\set('shared_files', $sharedFiles);

        $sharedDirs = [
            "{{app_dir}}pub/media",
            "{{app_dir}}var/log",
            "{{app_dir}}var/session",
            "{{app_dir}}var/composer_home",
            "{{app_dir}}var/n98_integration",
        ];
        \Deployer\set('shared_dirs', $sharedDirs);

        $writeDirs = [
            "{{app_dir}}var",
            "{{app_dir}}pub/static'",
            "{{app_dir}}pub/media'",
        ];
        \Deployer\set('writable_dirs', $writeDirs);

        \Deployer\set('ssh_type', 'native');
        \Deployer\set('ssh_multiplexing', true);

        \Deployer\set('bin/n98_magerun2', 'n98-magerun2');
        \Deployer\set('webserver_user', 'www-data');
        \Deployer\set('webserver_group', 'www-data');

        \Deployer\set('phpfpm_service', 'php7.0-fpm');
        \Deployer\set('nginx_service', 'nginx');
    }
}