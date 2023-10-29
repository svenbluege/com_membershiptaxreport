<?php
namespace Svenbluege\Component\MembershipProTaxReport\Administrator\View\Moss;
use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Toolbar\Toolbar;

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

        $bar = Toolbar::getInstance('toolbar');
        $bar->appendButton('Link', 'folder', 'All',  Route::_('index.php?option=com_membershiptaxreport&view=all'), false);
        $bar->appendButton('Link', 'folder', 'VIES',  Route::_('index.php?option=com_membershiptaxreport&view=vies'), false);
        $bar->appendButton('Link', 'folder', 'MOSS',  Route::_('index.php?option=com_membershiptaxreport&view=moss'), false);

	    parent::display($tpl);
	}

}

