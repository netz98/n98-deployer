<?php
/**
 * @copyright Copyright (c) 2017 netz98 GmbH (http://www.netz98.de)
 *
 * @see PROJECT_LICENSE.txt
 */

namespace Deployer;

use N98\Deployer\TestFramework\ConfigMock;
use N98\Deployer\TestFramework\DeployerMock;

function set($key, $value)
{
    ConfigMock::set($key, $value);
}

function get($key)
{
    return ConfigMock::get($key);
}

function cd($dir)
{
}

function run()
{
    $callback = DeployerMock::getCallback('run');
    $callback(...func_get_args());
}