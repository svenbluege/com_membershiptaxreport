<?php
namespace Svenbluege\Component\MembershipProTaxReport\Administrator\View\Revenue;
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

class HtmlView extends \Joomla\CMS\MVC\View\HtmlView
{

    /**
     * The figures which can be shown in the year/month matrix.
     */
    public static $metrics = [
        'net_amount'    => 'Net Amount',
        'tax_amount'    => 'Tax Amount',
        'gross_amount'  => 'Payable Amount',
        'subscriptions' => 'Subscriptions'
    ];

	function display($tpl = null)
	{
        $app = Factory::getApplication();

        /**
         * @var \Svenbluege\Component\MembershipProTaxReport\Administrator\Model\RevenueModel $model
         */
        $model = $this->getModel();

        $this->statistics = $model->getStatistics();

        $this->metric = $app->input->getCmd('metric', 'net_amount');
        if (!isset(self::$metrics[$this->metric])) {
            $this->metric = 'net_amount';
        }

        // the year 0 shows all years side by side. Without a request we show the most recent year.
        $this->year = $app->input->getInt('year', -1);
        if ($this->year == -1) {
            $this->year = empty($this->statistics->years) ? 0 : max($this->statistics->years);
        }
        if ($this->year != 0 && !isset($this->statistics->months[$this->year])) {
            $this->year = 0;
        }

        MembershipTaxReport::addToolbarLinks();

	    parent::display($tpl);
	}

}
