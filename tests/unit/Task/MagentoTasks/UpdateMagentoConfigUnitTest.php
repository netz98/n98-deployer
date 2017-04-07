<?php
/**
 * @copyright Copyright (c) 2017 netz98 GmbH (http://www.netz98.de)
 *
 * @see PROJECT_LICENSE.txt
 */

namespace N98\Deployer\Test\Task\MagentoTasks;

use N98\Deployer\Task\MagentoTasks;
use N98\Deployer\TestFramework\DeployerMock;

/**
 * UpdateMagentoConfigUnitTest
 *
 * @runInSeparateProcess
 * @runTestsInSeparateProcesses
 */
class UpdateMagentoConfigUnitTest extends \PHPUnit_Framework_TestCase
{
    const CONFIG_STORE_ENV = 'unit_test/custom/env';
    const CONFIG_STORE_DIR = 'unit_test/custom/dir';

    const EXPECTED_ARG_NUM = 5;

    /**
     * @test
     */
    public function itShouldTriggerConfigImport()
    {
        DeployerMock::addCallback(
            'run',
            function ($param) {
                $params = explode(' ', $param);

                \PHPUnit_Framework_Assert::assertCount(self::EXPECTED_ARG_NUM, $params);

                \PHPUnit_Framework_Assert::assertEquals(UpdateMagentoConfigIntegrationTest::CONFIG_STORE_DIR, $params[3]);
                \PHPUnit_Framework_Assert::assertEquals(UpdateMagentoConfigIntegrationTest::CONFIG_STORE_ENV, $params[4]);
            }
        );

        \Deployer\set('config_store_env', self::CONFIG_STORE_ENV);
        \Deployer\set('config_store_dir', self::CONFIG_STORE_DIR);

        MagentoTasks::updateMagentoConfig();
    }
}
