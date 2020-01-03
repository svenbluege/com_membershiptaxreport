<?php
/**
 * @package     Sven.Bluege
 * @subpackage  com_membershiptaxreport
 *
 * @copyright   Copyright (C) 2005 - 2019 Sven Bluege All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

function hasGeoIPPlugin()
{
    static $result = null;

    if (is_null($result))
    {
        $db = JFactory::getDbo();

        $query = $db->getQuery(true)
            ->select('COUNT(*)')
            ->from($db->qn('#__extensions'))
            ->where($db->qn('type') . ' = ' . $db->q('plugin'))
            ->where($db->qn('folder') . ' = ' . $db->q('system'))
            ->where($db->qn('element') . ' = ' . $db->q('akgeoip'));
        $db->setQuery($query);
        $result = $db->loadResult();
    }

    return ($result != 0);
}

function GeoIPDBNeedsUpdate($maxAge = 15)
{
    $needsUpdate = false;

    if (!hasGeoIPPlugin())
    {
        return $needsUpdate;
    }

    // Get the modification time of the database file
    $filePath = JPATH_ROOT . '/plugins/system/akgeoip/db/GeoLite2-Country.mmdb';
    $modTime = @filemtime($filePath);

    // This is now
    $now = time();

    // Minimum time difference we want (15 days) in seconds
    if ($maxAge <= 0)
    {
        $maxAge = 15;
    }

    $threshold = $maxAge * 24 * 3600;

    // Do we need an update?
    $needsUpdate = ($now - $modTime) > $threshold;

    return $needsUpdate;
}

if (!GeoIPDBNeedsUpdate()) {
?>

        <div class="well">
            <h3>
                GEO IP Database
            </h3>

            <p>
                Datebase is outdated and needs an update.
            </p>

            <a class="btn"
               href="index.php?option=com_membershiptaxreport&view=all&task=geoipupdate.updategeoip&<?php echo JSession::getFormToken() ?>=1">
                Update
            </a>
        </div>

<?php
}

