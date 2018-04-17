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
        \Deployer\Deployer::addDefault('readlink_bin', 'readlink');

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
        $hasReleaseDir = \Deployer\test('[ -d {{deploy_path}}/release ]');
        if ($hasReleaseDir) {
            $releaseDirToDelete = \Deployer\run('readlink {{deploy_path}}/release');
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
        $hasStableRelease = \Deployer\test("[ -d $path/current/ ]");
        if ($hasStableRelease) {
            if (PHP_OS === 'Darwin') {
                $releasePathStable = (string)\Deployer\run("{{readlink_bin}} -n $path/current");
            }
            else {
                $releasePathStable = (string)\Deployer\run("{{readlink_bin}} -f $path/current/");
            }
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
            $releaseClean = self::cleanString($release);
            $releaseName = $releaseClean . '-' . date('YmdHis');
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
        $cmdTemplate = '[ -d {{deploy_path}}/releases/%s ]';
        $cmd = sprintf($cmdTemplate, $releaseName);
        while (\Deployer\test($cmd)) {
            $releaseName = $release . '-' . ++$i;
            $cmd = sprintf($cmdTemplate, $releaseName);
        }

        return $releaseName;
    }

    /**
     * Clean a string from special characters and only leave letters, digits, _ and -
     *
     * @param $string
     *
     * @return string
     */
    protected static function cleanString($string)
    {
        // Replace all spaces with hyphens.
        $result = str_replace(' ', '_', $string);

        // Remove special chars.
        $result = preg_replace('/[^A-Za-z0-9\.\-]/', '-', $result);

        return $result;
    }
}
