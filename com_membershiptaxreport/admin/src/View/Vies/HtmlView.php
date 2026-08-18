<?php
namespace Svenbluege\Component\MembershipProTaxReport\Administrator\View\Vies;
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

	function display($tpl = null)
	{
        $app = Factory::getApplication();

        /**
         * @var MembershiptaxreportModelMoss $model
         */
        $model = $this->getModel();

        $this->month = $app->input->getInt('month', date('n'));
        $this->year = $app->input->getInt('year', date('Y'));

        $this->subscriptions = $model->getSubscriptions($this->year, $this->month);


        MembershipTaxReport::addToolbarLinks();


        parent::display($tpl);
	}

}

