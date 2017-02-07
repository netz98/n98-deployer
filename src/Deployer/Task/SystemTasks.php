<?php
/**
 * @copyright Copyright (c) 2017 netz98 GmbH (http://www.netz98.de)
 *
 * @see LICENSE
 */

namespace N98\Deployer\Task;

use N98\Deployer\Registry as Deployer;

/**
 * SystemTasks
 */
class SystemTasks extends TaskAbstract
{
    const TASK_PHP_FPM_RESTART = 'sys:php-fpm:restart';
    const TASK_NGINX_RESTART = 'sys:nginx:restart';

    public static function register()
    {
        Deployer::task(
            self::TASK_PHP_FPM_RESTART, 'Restart php-fpm service',
            function () { SystemTasks::restartPhpFpm(); },
            ['staging', 'production']
        );
        Deployer::task(
            self::TASK_NGINX_RESTART, 'Restart nginx service',
            function () { SystemTasks::restartNginx(); },
            ['staging', 'production']
        );
    }

    /**
     * Restart php-fpm using phpfpm_service variable
     */
    public static function restartPhpFpm()
    {
        $service = \Deployer\get('phpfpm_service');
        \Deployer\run("sudo service $service restart");
    }

    /**
     * Restart Nginx using nginx_service variable
     */
    public static function restartNginx()
    {
        $service = \Deployer\get('nginx_service');
        \Deployer\run("sudo service $service restart");
    }

}