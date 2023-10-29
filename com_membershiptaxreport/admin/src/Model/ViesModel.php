<?php
namespace Svenbluege\Component\MembershipProTaxReport\Administrator\Model;
use Joomla\CMS\Factory;

/**
 * @package     Sven.Bluege
 * @subpackage  com_membershiptaxreport
 *
 * @copyright   Copyright (C) 2005 - 2019 Sven Bluege All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

class ViesModel extends MossModel
{

    public function getSubscriptions($year, $month) {
        $db     = Factory::getDbo();
        $query  = $this->getSubscriberQuery($year, $month);
        $query ->where('tax_amount = 0');
        $db->setQuery($query);
        return $db->loadObjectList();
    }


}
