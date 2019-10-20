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

require_once (JPATH_SITE.'/components/com_osmembership/helper/euvat.php');

class MembershiptaxreportModelMoss extends JModelAdmin
{


    /**
     * Abstract method for getting the form from the model.
     *
     * @param array $data Data for the form.
     * @param boolean $loadData True if the form is to load its own data (default case), false if not.
     *
     * @return  \JForm|boolean  A \JForm object on success, false on failure
     *
     * @since   1.6
     */
    public function getForm($data = array(), $loadData = true)
    {
        return null;
    }

    private function getMonthCondition($month) {
        switch($month) {
            case 13: return '1,2,3'; break;
            case 14: return '4,5,6'; break;
            case 15: return '7,8,9'; break;
            case 16: return '10,11,12'; break;
            case 17: return '1,2,3,4,5,6,7,8,9,10,11,12'; break;
            default: return (int) $month; break;
        }
    }

    private function getEUVATCountryCondition() {
        $countryCodes = OSMembershipHelperEuvat::$europeanUnionVATInformation;
        return implode(',',array_map(function ($item) {return JFactory::getDbo()->quote($item[0]); } , $countryCodes));
    }

    protected function getSubscriberQuery($year, $month) {
        $db     = JFactory::getDbo();
        $query  = $db->getQuery(true)
            ->select('*, c.name as countryname, fv.field_value as vat_number, s.amount-s.discount_amount as amount')
            ->from('#__osmembership_subscribers s left join #__osmembership_countries c on (s.country = c.name OR s.country = c.country_2_code)')
            ->join('LEFT', '#__osmembership_field_value fv on s.id = fv.subscriber_id' )
            ->where('MONTH(created_date) in (' . $this->getMonthCondition($month) .')'
                . ' AND YEAR(created_date) = '. (int)$year
                . ' AND country in ('. $this->getEUVATCountryCondition() .')'
                . ' AND (s.published = 1 OR s.published = 2)'
            )
            ->order('country_2_code, created_date');

        return $query;
    }

    public function getSubscriptions($year, $month) {
        $db     = JFactory::getDbo();
        $query  = $this->getSubscriberQuery($year, $month);
        $query ->where('tax_amount > 0');

        echo $query->dump();

        $db->setQuery($query);
        return $db->loadObjectList();
    }


}
