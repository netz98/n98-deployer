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
class UpdateMagentoConfigIntegrationTest extends \PHPUnit_Framework_TestCase
{
    const EXPECTED_ARG_NUM = 5;

    /**
     * @test
     */
    public function itShouldTriggerConfigImport()
    {
        DeployerMock::addCallback(
            'run',
            function ($param) {
                $commands = explode('&&', $param);

                \PHPUnit_Framework_Assert::assertCount(2, $commands);

                $cdCommand = $commands[0];

                $configImportCommand = trim($commands[1], ' ()');

                $params = explode(' ', $configImportCommand);

                \PHPUnit_Framework_Assert::assertCount(self::EXPECTED_ARG_NUM, $params);

                $configDir = TEST_DEPLOYER_RELEASE_PATH . '/config/store';
                \PHPUnit_Framework_Assert::assertEquals($configDir, $params[3]);
                \PHPUnit_Framework_Assert::assertEquals(TEST_CONFIG_STORE_ENV, $params[4]);
            }
        );

        MagentoTasks::updateMagentoConfig();
    }
}
