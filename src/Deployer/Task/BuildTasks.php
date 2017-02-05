<?php
/**
 * @copyright Copyright (c) 2017 netz98 GmbH (http://www.netz98.de)
 *
 * @see LICENSE
 */

namespace N98\Deployer\Task;

use N98\Deployer\Registry as Deployer;

/**
 * BuildTasks
 */
class BuildTasks extends TaskAbstract
{
    const TASK_UPLOAD_ARTIFACTS = 'build:upload_artifacts';
    const TASK_SHARED_DIRS_GENERATE = 'build:shared_dirs_generate';
    const TASK_CHANGE_OWNER_AND_MODE = 'build:change_owner_and_mode';
    const TASK_LINK_ENV_CONFIG = 'build:link_env_config';

    const PATH_APP_ETC_ENV_PHP = 'app/etc/env.php';

    public static function register()
    {
        Deployer::task(
            BuildTasks::TASK_UPLOAD_ARTIFACTS, 'upload artifacts',
            function () { BuildTasks::uploadArtifacts(); }
        );
        Deployer::task(
            BuildTasks::TASK_SHARED_DIRS_GENERATE, 'Generate Shared Dirs',
            function () { BuildTasks::generateSharedDirs(); }
        );
        Deployer::task(
            BuildTasks::TASK_CHANGE_OWNER_AND_MODE, 'change owner and mode',
            function () { BuildTasks::changeOwnerAndMode(); }
        );
        Deployer::task(
            BuildTasks::TASK_LINK_ENV_CONFIG, 'Link env.php',
            function () { BuildTasks::linkEnvConfig(); }
        );
    }

    public static function uploadArtifacts()
    {
        $artifacts = \Deployer\get('magento_build_artifacts');

        foreach ($artifacts as $artifact) {
            self::uploadAndExtract($artifact);
        }
    }

    /**
     * Generate Shared Dirs
     *
     * @todo validate if this method is still needed? recipe/deploy/shared.php should suffice
     */
    public static function generateSharedDirs()
    {
        /** @var array $dirs */
        $dirs = \Deployer\get('shared_dirs');

        foreach ($dirs as $dir) {
            $cmd = "mkdir -p {{deploy_path}}/shared/$dir";
            \Deployer\run($cmd);
        }
    }

    /**
     * Fix File Ownership and access rights for both deploy user and webserver_user
     */
    public static function changeOwnerAndMode()
    {
        $dirs = \Deployer\get('change_owner_mode_dirs');

        if (empty($dirs)) {
            return;
        }

        foreach ($dirs as $key => $dirData) {
            $dir = $dirData['dir'];
            $owner = $dirData['owner'];
            $mode = $dirData['mode'];

            \Deployer\run("sudo chown -RH $owner $dir");
            \Deployer\run("sudo chmod -R $mode $dir");
        }
    }

    /**
     * Symlinks Magento env.php from shared to current release
     */
    public static function linkEnvConfig()
    {
        $file =self::PATH_APP_ETC_ENV_PHP;

        \Deployer\run("{{bin/symlink}} {{shared_path_app}}/$file {{release_path_app}}/$file");
    }

    /**
     * Upload and Extract a Tar file
     *
     * @param $tarFile
     */
    protected static function uploadAndExtract($tarFile)
    {
        $releasePath = '{{release_path}}';

        \Deployer\upload("shop/$tarFile", "$releasePath/$tarFile");
        \Deployer\run("cd $releasePath; tar xfz $releasePath/$tarFile");
        \Deployer\run("rm $releasePath/$tarFile");
    }

}