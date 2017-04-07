<?php
/**
 * @copyright Copyright (c) 1999-2016 netz98 GmbH (http://www.netz98.de)
 *
 * @see PROJECT_LICENSE.txt
 */

namespace Deployer;

use Deployer\Builder\BuilderInterface;
use Deployer\Server\Builder;
use Deployer\Server\Configuration;
use Deployer\Server\Environment;
use N98\Deployer\RoleManager;


/**
 * @param string $name
 * @return BuilderInterface
 */
function mockServer($name)
{
    $deployer = Deployer::get();

    $env = new Environment();
    $config = new Configuration($name, 'localhost'); // Builder requires server configuration.
    $server = new \N98\Deployer\Server\ServerMock($config);

    $deployer->servers->set($name, $server);
    $deployer->environments->set($name, $env);

    return new Builder($config, $env);
}

$local = mockServer('local');
$local->user('mwalter');
$local->set('deploy_path', '/Volumes/Data/Entwicklung/Workspace/mwltr/magedeploy2/magedeploy2_test_server');
$local->stage('dev');

$local->set('config_store_env', TEST_CONFIG_STORE_ENV);
$local->set('webserver_user', 'mwalter');
$local->set('webserver_group', 'admin');
$local->set('bin/n98_magerun2', '/usr/local/bin/n98-magerun2.phar');

RoleManager::addServerToRoles('local', ['web', 'db']);

$initTest = function () {
    $deployer = Deployer::get();
    $server = $deployer->servers->get('local');

    $env = $deployer->environments->get('local');
    // $env->set('config_store_env', 'base');

    $input = new \Symfony\Component\Console\Input\ArgvInput();
    $output = new \Symfony\Component\Console\Output\ConsoleOutput();
    $context = new \Deployer\Task\Context($server, $env, $input, $output);

    \Deployer\Task\Context::push($context);
};
$initTest();
unset($initTest, $local);