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
class ActivateMagentoCacheUnitTest extends \PHPUnit_Framework_TestCase
{
    const EXPECTED_ARG_NUM = 3;

    /**
     * @test
     */
    public function itShouldDisableCache()
    {
        DeployerMock::addCallback(
            'run',
            function ($param) {
                $params = explode(' ', $param);

                \PHPUnit_Framework_Assert::assertCount(self::EXPECTED_ARG_NUM, $params);

                \PHPUnit_Framework_Assert::assertEquals('cache:disable', $params[2]);
            }
        );

        MagentoTasks::activateMagentoCache(false);
    }

    /**
     * @test
     */
    public function itShouldEnableCache()
    {
        DeployerMock::addCallback(
            'run',
            function ($param) {
                $params = explode(' ', $param);

                \PHPUnit_Framework_Assert::assertCount(self::EXPECTED_ARG_NUM, $params);

                \PHPUnit_Framework_Assert::assertEquals('cache:enable', $params[2]);
            }
        );

        MagentoTasks::activateMagentoCache(true);
    }

}
