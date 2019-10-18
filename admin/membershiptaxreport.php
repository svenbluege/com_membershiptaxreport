<?php
/**
 * @package     Sven.Bluege
 * @subpackage  com_eventgallery
 *
 * @copyright   Copyright (C) 2005 - 2019 Sven Bluege All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die('Restricted access');


$view = JFactory::getApplication()->input->get('view');
$task = JFactory::getApplication()->input->get('task');

// Execute the task.
$controller	= JControllerLegacy::getInstance('Membershiptaxreport');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
