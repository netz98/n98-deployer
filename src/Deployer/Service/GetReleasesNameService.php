<?php
/**
 * @copyright Copyright (c) 1999-2016 netz98 new media GmbH (http://www.netz98.de)
 *
 * @see LICENSE
 */

namespace N98\Deployer\Service;

/**
 * GetReleasesNameService
 */
class GetReleasesNameService
{
    /**
     * Determine the release name by branch or tag, otherwise uses datetime string
     *
     * this method is an overwrite of the Deployer release-name logic
     *
     * @return string
     */
    public static function execute()
    {
        $release = null;

        // Get release-name from branch
        $input = \Deployer\input();
        if ($input->hasOption('branch')) {
            $branch = $input->getOption('branch');
            if (!empty($branch)) {
                $release = $branch;
            }
        }

        if ($release !== null) {
            return $release;
        }

        // Get release-name from tag
        $input = \Deployer\input();
        if ($input->hasOption('tag')) {
            $tag = $input->getOption('tag');
            if (!empty($tag)) {
                $release = $tag;
            }
        }

        if ($release !== null) {
            return $release;
        }

        $release = date('Ymdhis');

        return $release;
    }

}