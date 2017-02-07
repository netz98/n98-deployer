<?php
/**
 * @copyright Copyright (c) 2017 netz98 GmbH (http://www.netz98.de)
 *
 * @see LICENSE
 */

namespace N98\Deployer\Task;

use N98\Deployer\Registry as Deployer;

/**
 * DeployTasks
 */
class DeployTasks extends TaskAbstract
{
    const TASK_INITIALIZE = 'deploy:initialize';
    const TASK_ROLLBACK = 'rollback'; // Overwriting the existing one

    public static function register()
    {
        Deployer::task(
            DeployTasks::TASK_INITIALIZE, 'initialize',
            function () { DeployTasks::initialize(); }
        );
        Deployer::task(
            DeployTasks::TASK_ROLLBACK, 'rollback to stable release previous to deploy',
            function () { DeployTasks::rollback(); }
        );
    }

    /**
     * Initialize
     */
    public static function initialize()
    {
        self::initStableRelease();
        self::initReleaseName();
    }

    /**
     * Rollback to previous stable release
     *
     * This method is basically copied from original Deployer rollback,
     * but was quite heavily optimized to suit our needs
     *
     * @see deployer/deployer/recipe/deploy/rollback.php
     */
    public static function rollback()
    {
        $hasReleaseDir = \Deployer\has('release_path_stable');
        if (!$hasReleaseDir) {
            \Deployer\writeln('<comment>No stable release we could rollback to.</comment>');

            return;
        }

        // @todo refactor to method
        $releaseDir = \Deployer\get('release_path_stable');

        // There was no current
        if (empty($releaseDir)) {
            \Deployer\writeln('<comment>No stable release we could rollback to.</comment>');

            return;
        }

        // Symlink to stable release.
        \Deployer\cd('{{deploy_path}}');
        \Deployer\run("{{bin/symlink}} $releaseDir current");

        // Remove erronous release
        $hasReleaseDir = \Deployer\run("if [ -d {{deploy_path}}/release ]; then echo 'true'; fi")->toBool();
        if ($hasReleaseDir) {
            $releaseDirToDelete = \Deployer\run('readlink {{deploy_path}}/release')->toString();
            if ($releaseDirToDelete) {
                \Deployer\run("rm -rf {$releaseDirToDelete}"); // Delete release.
                \Deployer\run('rm {{deploy_path}}/release'); // Delete symlink.
            }
        }

        if (\Deployer\isVerbose()) {
            \Deployer\writeln("Rollback to `{$releaseDir}` release was successful.");
        }
    }

    /**
     * Throws an Exception to simulate some scenarios.
     *
     * USE ONLY IN DEV ENVIRONMENTS
     * SHOULD NOT BE USED IN PRODUCTION OR STAGING
     *
     * @throws \RuntimeException
     */
    public static function throwException()
    {
        throw new \RuntimeException('ERROR');
    }

    /**
     * Detect the path to current release before actual deployment starts
     *
     * This is done so we have it later on during rollback and maybe other actions
     */
    protected static function initStableRelease()
    {
        $releasePathStable = '';
        $path = \Deployer\get('deploy_path');
        $hasReleasePathStable = \Deployer\run("if [ -d $path/current/ ]; then echo 'true'; fi")->toBool();
        if ($hasReleasePathStable) {
            $releasePathStable = (string)\Deployer\run("realpath $path/current/");
            \Deployer\set('release_path_stable', $releasePathStable);
        }
        if (\Deployer\isVerbose()) {
            \Deployer\writeln("release_path_stable = '{$releasePathStable}'");
        }
    }

    /**
     * Initialize a unique release-name checking if the release-name already exists and then postfixing it
     *
     * this is part of the overwrite of Deployer's own release_name logic
     */
    protected static function initReleaseName()
    {
        // Ensure the release-name is unique otherwise there are weird consequences in deployer
        // them being dep/releases with wrong versions, deployments to same release, …
        $release = \Deployer\get('release_name');

        $isVersion = version_compare($release, '0.0.1', '>=');
        if ($isVersion === true) {
            $releaseName = self::getUniqueReleaseName($release);
        } else {
            $releaseName = $release . '-' . date('YmdHis');
        }

        \Deployer\set('release_name', $releaseName);
        \Deployer\writeln("Deploying $releaseName to releases/$releaseName");
    }

    /**
     * Determines a unique release-name
     *
     * @param string $release
     *
     * @return string
     */
    protected static function getUniqueReleaseName($release)
    {
        // Prevent duplicate release names
        $i = 0;
        $releaseName = $release;
        while (\Deployer\run("if [ -d {{deploy_path}}/releases/$releaseName ]; then echo 'true'; fi")->toBool()) {
            $releaseName = $release . '-' . ++$i;
        }

        return $releaseName;
    }

}