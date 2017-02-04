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
    const TASK_FIX_FILE_OWNERSHIP = 'build:fix_file_ownership';
    const TASK_LINK_ENV_CONFIG = 'build:link_env_config';

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
            BuildTasks::TASK_FIX_FILE_OWNERSHIP, 'fix file-ownership',
            function () { BuildTasks::fixFileOwnership(); }
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
     */
    public static function generateSharedDirs()
    {
        /** @var array $dirs */
        $dirs = \Deployer\get('shared_dirs');

        $sharedDir = self::$sharedDir;
        foreach ($dirs as $dir) {
            $cmd = "mkdir -p $sharedDir/$dir";
            \Deployer\run($cmd);
        }
    }

    /**
     * Fix File Ownership and access rights for both deploy user and webserver-user
     */
    public static function fixFileOwnership()
    {
        $webserverUser = \Deployer\get('webserver-user');
        $webserverGroup = \Deployer\get('webserver-group');
        $srcDir = self::$srcDir;
        $sharedDir = self::$sharedDir;

        // Change File ownership to pub/static, it has to be writable even in production mode as there is test
        \Deployer\run("sudo chown -RH $webserverUser:$webserverGroup $srcDir/pub/static");
        \Deployer\run("sudo chmod -R g+w $srcDir/pub/static");

        // Change file ownership and acl in var dir (not all dirs under var are linked)
        \Deployer\run("sudo chown -RH $webserverUser:$webserverGroup $srcDir/var");
        \Deployer\run("sudo chmod -R 775 $srcDir/var");

        // Change file ownership and acl in shared directly (in case chmod has no -H option)
        $dirs = ['src/var', 'src/pub/media'];
        foreach ($dirs as $dir) {
            \Deployer\run("sudo chown -RH $webserverUser:$webserverGroup $sharedDir/$dir");
            \Deployer\run("sudo chmod -R 775 $sharedDir/$dir");
        }
    }

    /**
     * Symlinks Magento env.php from shared to current release
     */
    public static function linkEnvConfig()
    {
        $file = 'src/app/etc/env.php';
        $sharedPath = self::$sharedDir;

        \Deployer\run("{{bin/symlink}} $sharedPath/$file {{release_path}}/$file");
    }

    /**
     * Upload and Extract a Tar file
     *
     * @param $tarFile
     */
    protected static function uploadAndExtract($tarFile)
    {
        $releasePath = self::$releaseDir;
        \Deployer\upload("shop/$tarFile", "$releasePath/$tarFile");
        \Deployer\run("cd $releasePath; tar xfz $releasePath/$tarFile");
        \Deployer\run("rm $releasePath/$tarFile");
    }

}