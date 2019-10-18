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

jimport( 'joomla.application.component.view');

/** @noinspection PhpUndefinedClassInspection */
class MembershiptaxreportViewVies extends JViewLegacy
{

	function display($tpl = null)
	{
        $app = JFactory::getApplication();

        /**
         * @var MembershiptaxreportModelMoss $model
         */
        $model = $this->getModel();

        $this->month = $app->input->getInt('month', date('n'));
        $this->year = $app->input->getInt('year', date('Y'));

        $this->subscriptions = $model->getSubscriptions($this->year, $this->month);


        $bar = JToolbar::getInstance('toolbar');
        $bar->appendButton('Link', 'folder', 'VIES',  JRoute::_('index.php?option=com_membershiptaxreport&view=vies'), false);
        $bar->appendButton('Link', 'folder', 'MOSS',  JRoute::_('index.php?option=com_membershiptaxreport&view=moss'), false);


        parent::display($tpl);
	}

}

