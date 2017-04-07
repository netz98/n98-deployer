<?php
/**
 * @copyright Copyright (c) 2017 netz98 GmbH (http://www.netz98.de)
 *
 * @see PROJECT_LICENSE.txt
 */
$rootDir = getcwd();

require_once $rootDir . '/vendor/autoload.php';

// Include custom Deployer TestFramework
require_once $rootDir . '/tests/framework/autoload.php';
require_once __DIR__ . '/deployer.php';