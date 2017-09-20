<?php
/**
 * @copyright Copyright (c) 1999-2016 netz98 new media GmbH (http://www.netz98.de)
 *
 * @see LICENSE
 */

namespace N98\Deployer\Service;

use Deployer\Type\Csv as CsvType;

/**
 * GetReleasesListService
 */
class GetReleasesListService
{
    /**
     * Returns a list of releases on server.
     *
     * @return array
     */
    public static function execute()
    {
        \Deployer\cd('{{deploy_path}}');

        // If there is no releases return empty list.
        $cmdReleaseDirs = '[ -d releases ] && [ "$(ls -A releases)" ]';
        $hasReleaseDirs = \Deployer\test($cmdReleaseDirs);
        if (!$hasReleaseDirs) {
            return [];
        }

        // Will list only dirs in releases.
        $releasesRaw = \Deployer\run('cd releases && ls -t -d */');

        $list = explode("\n", $releasesRaw);

        // Prepare list.
        $list = array_map(function ($release) { return basename(rtrim($release, '/')); }, $list);

        $releases = []; // Releases list.

        // Collect releases based on .dep/releases info.
        // Other will be ignored.

        $hasReleasesList = \Deployer\test('[ -f .dep/releases ]');
        if (!$hasReleasesList) {
            return $releases;
        }

        // we do not filter the keep_releases here, as we want a full list
        $csv = \Deployer\run('cat .dep/releases');

        $metainfo = CsvType::parse($csv);

        for ($i = count($metainfo) - 1; $i >= 0; --$i) {
            if (is_array($metainfo[$i]) && count($metainfo[$i]) >= 2) {
                list($date, $release) = $metainfo[$i];
                $index = array_search($release, $list, true);
                if ($index !== false) {
                    $releases[] = $release;
                    unset($list[$index]);
                }
            }
        }

        return $releases;
    }

}
