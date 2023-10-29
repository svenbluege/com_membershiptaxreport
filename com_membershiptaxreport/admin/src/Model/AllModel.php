<?php
namespace Svenbluege\Component\MembershipProTaxReport\Administrator\Model;
use Joomla\CMS\Factory;
use Svenbluege\Component\MembershipProTaxReport\Administrator\Helper\MembershipTaxReport;

/**
 * @package     Sven.Bluege
 * @subpackage  com_membershiptaxreport
 *
 * @copyright   Copyright (C) 2005 - 2019 Sven Bluege All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

class AllModel extends MossModel
{

    protected function getSubscriberQuery($year, $month) {
        $db     = Factory::getDbo();
        $query  = $db->getQuery(true)
            ->select('*, c.name as countryname, fv.field_value as vat_number, s.amount-s.discount_amount as amount')
            ->from('#__osmembership_subscribers s left join #__osmembership_countries c on (s.country = c.name OR s.country = c.country_2_code)')
            ->join('LEFT', '#__osmembership_field_value fv on s.id = fv.subscriber_id' )
            ->where('MONTH(created_date) in (' . MembershipTaxReport::getMonthCondition($month) .')'
                . ' AND YEAR(created_date) = '. (int)$year
                . ' AND (s.published = 1 OR s.published = 2)'
            )
            ->order('country_2_code, created_date');

        return $query;
    }

    public function getSubscriptions($year, $month) {
        $db     = Factory::getDbo();
        $query  = $this->getSubscriberQuery($year, $month);

        $db->setQuery($query);
        return $db->loadObjectList();
    }


}
