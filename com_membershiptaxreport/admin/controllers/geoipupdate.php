<?php

/**
 * @package     Sven.Bluege
 * @subpackage  com_membershiptaxreport
 *
 * @copyright   Copyright (C) 2005 - 2019 Sven Bluege All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die('Restricted access');

class MembershiptaxreportControllerGeoipupdate extends JControllerForm
{
    public function updategeoip()
    {
        if ($this->csrfProtection)
        {
            $this->csrfProtection();
        }

        // Load the GeoIP library if it's not already loaded
        if (!class_exists('AkeebaGeoipProvider'))
        {
            if (@file_exists(JPATH_PLUGINS . '/system/akgeoip/lib/akgeoip.php'))
            {
                if (@include_once JPATH_PLUGINS . '/system/akgeoip/lib/vendor/autoload.php')
                {
                    @include_once JPATH_PLUGINS . '/system/akgeoip/lib/akgeoip.php';
                }
            }
        }

        $geoip = new \AkeebaGeoipProvider();
        $result = $geoip->updateDatabase();

        $url = 'index.php?option=com_membershiptaxreport';

        if ($result === true)
        {
            $msg = JText::_('Geo IP Update complete');
            $this->setRedirect($url, $msg);
        }
        else
        {
            $this->setRedirect($url, $result, 'error');
        }
    }
}
