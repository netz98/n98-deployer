<?php
/**
 * @copyright Copyright (c) 2017 netz98 GmbH (http://www.netz98.de)
 *
 * @see LICENSE
 */

namespace N98\Deployer;

/**
 * Registry
 */
class Registry
{
    /**
     * Register All N98 Tasks
     *
     * @param array $tasks
     */
    public static function registerTasks(array $tasks)
    {
        // Register Tasks
        foreach ($tasks as $key => $task) {
            $roles = array_key_exists('roles', $task) ? $task['roles'] : null;
            self::registerTask($key, $task['desc'], $task['callback'], $roles);
        }
    }

    public static function task($code, $desc, \Closure $body, array $roles = null)
    {
        return self::registerTask($code, $desc, $body, $roles);
    }

    /**
     * @param string $code
     * @param string $desc
     * @param \Closure $body
     * @param array $roles
     *
     * @return \Deployer\Task\Task
     */
    public static function registerTask($code, $desc, \Closure $body, array $roles = null)
    {
        \Deployer\desc($desc);
        $task = \Deployer\task($code, $body);

        if (is_array($roles)) {
            $servers = RoleManager::getServerListByRoles($roles);

            $task->onlyOn($servers);
        }

        return $task;
    }

}