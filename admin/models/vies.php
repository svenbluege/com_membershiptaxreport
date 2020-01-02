<?php
/**
 * @package     Sven.Bluege
 * @subpackage  com_membershiptaxreport
 *
 * @copyright   Copyright (C) 2005 - 2019 Sven Bluege All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

require_once __DIR__.'/moss.php';

class MembershiptaxreportModelVies extends MembershiptaxreportModelMoss
{

    public function getSubscriptions($year, $month) {
        $db     = JFactory::getDbo();
        $query  = $this->getSubscriberQuery($year, $month);
        $query ->where('tax_amount = 0');
        $db->setQuery($query);
        return $db->loadObjectList();
    }


}
