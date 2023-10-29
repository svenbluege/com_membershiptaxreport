<?php
namespace Svenbluege\Component\MembershipProTaxReport\Administrator\Controller;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\FormController;

/**
 * @package     Sven.Bluege
 * @subpackage  com_membershiptaxreport
 *
 * @copyright   Copyright (C) 2005 - 2019 Sven Bluege All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die('Restricted access');

class GeoipupdateController extends FormController
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
            if (@file_exists(JPATH_PLUGINS . '/system/akgeoip/src/Extension/lib/akgeoip.php'))
            {
                if (@include_once JPATH_PLUGINS . '/system/akgeoip/src/Extension/lib/vendor/autoload.php')
                {
                    @include_once JPATH_PLUGINS . '/system/akgeoip/src/Extension/lib/akgeoip.php';
                }
            }
        }

        $geoip = new \AkeebaGeoipProvider();
        $result = $geoip->updateDatabase();

        $url = 'index.php?option=com_membershiptaxreport';

        if ($result === true)
        {
            $msg = Text::_('Geo IP Update complete');
            $this->setRedirect($url, $msg);
        }
        else
        {
            $this->setRedirect($url, $result, 'error');
        }
    }
}
