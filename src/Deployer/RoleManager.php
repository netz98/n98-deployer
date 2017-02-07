<?php
/**
 * @copyright Copyright (c) 2017 netz98 GmbH (http://www.netz98.de)
 *
 * @see LICENSE
 */

namespace N98\Deployer;

/**
 * RoleManager
 */
class RoleManager
{
    /**
     * Server Roles
     *
     * @var array
     */
    protected static $roles;

    /**
     * @param array $roles
     *
     * @return array
     */
    public static function getServerListByRoles(array $roles)
    {
        $servers = [];
        foreach ($roles as $roleCode) {
            $rolesForCode = RoleManager::getServersByRole($roleCode);

            foreach ($rolesForCode as $serverCode) {
                if (in_array($serverCode, $servers, true)) {
                    continue;
                }

                $servers[] = $serverCode;
            }
        }

        return $servers;
    }

    /**
     * Register Server Roles
     *
     * the roles provided will be used to classify the tasks into roles.
     * Roles is not a feature of deployer yet so this is kind of a workaround
     *
     * @param array $roles
     */
    public static function setServerRoles(array $roles)
    {
        self::$roles = $roles;
    }

    /**
     * Asign a Server to a Role
     *
     * @param string $server
     * @param array $roles
     */
    public static function addServerToRoles($server, array $roles)
    {
        foreach ($roles as $role) {
            self::$roles[$role][] = $server;
        }

    }

    /**
     * @param string $code
     *
     * @return array
     */
    public static function getServersByRole($code)
    {
        return self::$roles[$code];
    }

}