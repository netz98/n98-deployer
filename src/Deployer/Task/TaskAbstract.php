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
    protected static $pathReleaseDir = '{{release_path}}';

    /**
     * Path to Application Dir
     *
     * @var string
     */
    protected static $pathAppDir = null;

    /**
     * Path to Shared Dir
     *
     * @var string
     */
    protected static $pathSharedDir = '{{deploy_path}}/shared';

    /**
     * Get AppDir
     *
     * @return string
     */
    public static function getAppDir()
    {
        return \Deployer\get('app_dir');
    }

    /**
     * Get AppDir
     *
     * @return string
     */
    public static function getPathAppDir()
    {
        if (self::$pathAppDir === null) {
            // use releaseDir
            self::$pathAppDir = self::$pathReleaseDir;

            // use custom app dir if set
            if (!empty( self::getAppDir())) {
                self::$pathAppDir = '/' .  self::getAppDir();
            }
        }

        return self::$pathAppDir;
    }

    /**
     * Get ReleaseDir
     *
     * @return string
     */
    public static function getPathReleaseDir()
    {
        return self::$pathReleaseDir;
    }

    /**
     * Get SharedDir
     *
     * @return string
     */
    public static function getPathSharedDir()
    {
        return self::$pathSharedDir;
    }

}