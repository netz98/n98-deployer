<?php
/**
 * @copyright Copyright (c) 2017 netz98 GmbH (http://www.netz98.de)
 *
 * @see LICENSE
 */

namespace N98\Deployer\Task;

use N98\Deployer\Registry as Deployer;

/**
 * CleanupTasks
 */
class CleanupTasks extends TaskAbstract
{
    const TASK_CLEANUP = 'cleanup'; // Overwriting the existing one

    public static function register()
    {
        Deployer::task(self::TASK_CLEANUP, 'Cleanup old releases', function () { CleanupTasks::cleanup(); });
    }

    /**
     * Cleanup old releases
     *
     * This method is basically copied from original Deployer cleanup,
     * but was extended by sudo for removal of dir to prevent unwanted issues causing rollbacks be-cause of cleanups
     *
     * @see deployer/deployer/recipe/deploy/rollback.php
     */
    public static function cleanup()
    {
        /** @var string[] $releases */
        $releases = \Deployer\get('releases_list');

        $keep = \Deployer\get('keep_releases');

        if ($keep === -1) {
            // Keep unlimited releases.
            return;
        }

        while ($keep - 1 > 0) {
            array_shift($releases);
            --$keep;
        }

        foreach ($releases as $release) {
            \Deployer\run("sudo rm -rf {{deploy_path}}/releases/$release");
        }

        \Deployer\run('cd {{deploy_path}} && if [ -e release ]; then rm release; fi');
        \Deployer\run('cd {{deploy_path}} && if [ -h release ]; then rm release; fi');
    }

}