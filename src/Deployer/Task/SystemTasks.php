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
    const TASK_CRON_STOP = 'sys:cron:stop';
    const TASK_CRON_START = 'sys:cron:start';

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
        Deployer::task(
            self::TASK_CRON_STOP, 'Stop cron service',
            function () { SystemTasks::stopCron(); },
            ['staging', 'production']
        );
        Deployer::task(
            self::TASK_CRON_START, 'Start cron service',
            function () { SystemTasks::startCron(); },
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

    /**
     * Stop cron-daemon using cron_service variable
     */
    public static function stopCron()
    {
        $service = \Deployer\get('cron_service');
        \Deployer\run("sudo service $service stop");
    }

    /**
     * Start cron-daemon using cron_service variable
     */
    public static function startCron()
    {
        $service = \Deployer\get('cron_service');
        \Deployer\run("sudo service $service start");
    }

}