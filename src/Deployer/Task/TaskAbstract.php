<?php
/**
 * @copyright Copyright (c) 2017 netz98 GmbH (http://www.netz98.de)
 *
 * @see LICENSE
 */

namespace N98\Deployer\Task;

/**
 * TaskAbstract
 */
abstract class TaskAbstract
{
    /**
     * Path to Release Dir
     *
     * @var string
     */
    protected static $releaseDir = '{{release_path}}';

    /**
     * Path to Source Dir
     *
     * @var string
     */
    protected static $srcDir = '{{release_path}}/src';

    /**
     * Path to Shared Dir
     *
     * @var string
     */
    protected static $sharedDir = '{{deploy_path}}/shared';

}