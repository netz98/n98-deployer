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


const TEST_DEPLOYER_RELEASE_PATH = __DIR__ . '/release-path';
const TEST_CONFIG_STORE_ENV = 'base';

require_once 'boostrap_deployer.php';
require_once 'deploy.php';