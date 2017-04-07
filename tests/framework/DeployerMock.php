<?php
/**
 * @copyright Copyright (c) 2017 netz98 GmbH (http://www.netz98.de)
 *
 * @see PROJECT_LICENSE.txt
 */

namespace N98\Deployer\TestFramework;

class DeployerMock
{
    protected static $callbacks = [];

    public static function addCallback($method, $callback)
    {
        self::$callbacks[$method] = $callback;
    }

    public static function getCallback($method)
    {
        if (!array_key_exists($method, self::$callbacks)) {
            return null;
        }

        return self::$callbacks[$method];
    }
}
