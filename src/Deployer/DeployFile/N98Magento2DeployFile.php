<?php
/**
 * @copyright Copyright (c) 2017 netz98 new media GmbH (http://www.netz98.de)
 *
 * @see LICENSE
 */

namespace N98\Deployer\DeployFile;

/**
 * N98Magento2DeployFile
 */
class N98Magento2DeployFile extends Magento2DeployFile
{
    public static function configuration()
    {
        parent::configuration();

        $appDir = 'src/';

        \Deployer\set('app_dir', $appDir);

        $sharedFiles = [
            "{$appDir}app/etc/env.php",
        ];
        \Deployer\set('shared_files', $sharedFiles);

        $sharedDirs = [
            "{$appDir}pub/media",
            "{$appDir}var/log",
            "{$appDir}var/session",
            "{$appDir}var/composer_home",
            "{$appDir}var/n98_integration",
        ];
        \Deployer\set('shared_dirs', $sharedDirs);

        $writeDirs = [
            "{$appDir}var",
            "{$appDir}pub/static'",
            "{$appDir}pub/media'",
        ];
        \Deployer\set('writable_dirs', $writeDirs);

        \Deployer\set('ssh_type', 'native');

        \Deployer\set('bin/n98_magerun2', 'n98-magerun2');
        \Deployer\set('webserver-user', 'www-data');
        \Deployer\set('webserver-group', 'www-data');

        \Deployer\set('phpfpm_service', 'php7.0-fpm');
        \Deployer\set('nginx_service', 'nginx');

        $artifacts = [
            'config.tar.gz',
        ];
        \Deployer\add('magento_build_artifacts', $artifacts);

    }
}