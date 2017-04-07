<?php
/**
 * @copyright Copyright (c) 2017 netz98 GmbH (http://www.netz98.de)
 *
 * @see PROJECT_LICENSE.txt
 */

namespace N98\Deployer\TestFramework;

class ConfigMock
{
    protected static $config = [];

    public static function set($key, $value)
    {
        self::$config[$key] = $value;
    }

    public static function get($key)
    {
        return self::$config[$key];
    }
}