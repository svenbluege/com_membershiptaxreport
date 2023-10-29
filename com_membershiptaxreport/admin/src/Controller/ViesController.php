<?php
namespace Svenbluege\Component\MembershipProTaxReport\Administrator\Controller;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Controller\FormController;
use Svenbluege\Component\MembershipProTaxReport\Administrator\Helper\MembershipTaxReport;
use Svenbluege\Component\MembershipProTaxReport\Administrator\Library\ViesEntry;
use Svenbluege\Component\MembershipProTaxReport\Administrator\Model\MossModel;

/**
 * @package     Sven.Bluege
 * @subpackage  com_membershiptaxreport
 *
 * @copyright   Copyright (C) 2005 - 2019 Sven Bluege All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die('Restricted access');

class ViesController extends FormController
{
    public function getModel($name = 'Vies', $prefix ='', $config = array('ignore_request' => true))
    {
        return parent::getModel($name, $prefix, $config);
    }

    public function export()
    {
        /**
         * @var MossModel $model
         */

        $app = Factory::getApplication();
        $year = $app->input->getInt('year');
        $month = $app->input->getInt('month');
        $debugMode = $app->input->getBool('debug', false);

        $model = $this->getModel();
        $subscriptions = $model->getSubscriptions($year, $month);

        $monthName = MembershipTaxReport::monthToString($month);
        $filename = "vies_{$year}_$monthName.csv";

        header("Cache-Control: no-cache, must-revalidate"); //HTTP 1.1
        header("Pragma: no-cache"); //HTTP 1.0
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="'.$filename.'"');

        $headline = ViesEntry::getCSVHeader();

        $fp = fopen('php://output', 'wb');
        fputcsv($fp, $headline);

        /**
         * @var ViesEntry[] $entries;
         */
        $entries = [];

        foreach($subscriptions as $subscription) {
            $countryCode = $subscription->country_2_code;
            // remove the space in NL VAT numbers
            $vatNumber = str_replace(' ', '', $subscription->vat_number);
            $hash = $countryCode.$vatNumber;
            $netAmount = $subscription->amount;

            if (isset($entries[$hash])) {
                $entries[$hash]->addNetAmount($netAmount);
            } else {
                $viewEntry = new ViesEntry($countryCode, $vatNumber, $netAmount);
                $entries[$hash] = $viewEntry;
            }

        }

        foreach($entries as $key=>$entry) {
            fputcsv($fp, $entry->getCSVLine());
        }

        fclose($fp);

        die();
    }


}


