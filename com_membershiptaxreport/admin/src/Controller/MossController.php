<?php
namespace Svenbluege\Component\MembershipProTaxReport\Administrator\Controller;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Controller\FormController;
use Svenbluege\Component\MembershipProTaxReport\Administrator\Helper\MembershipTaxReport;
use Svenbluege\Component\MembershipProTaxReport\Administrator\Library\TaxCountry;

/**
 * @package     Sven.Bluege
 * @subpackage  com_membershiptaxreport
 *
 * @copyright   Copyright (C) 2005 - 2019 Sven Bluege All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die('Restricted access');

class MossController extends FormController
{
    public function getModel($name = 'Moss', $prefix ='', $config = array('ignore_request' => true))
    {
        return parent::getModel($name, $prefix, $config);
    }

    public function export()
    {
        /**
         * @var MembershiptaxreportModelMoss $model
         */

        $app = Factory::getApplication();
        $year = $app->input->getInt('year');
        $month = $app->input->getInt('month');
        $debugMode = $app->input->getBool('debug', false);

        $model = $this->getModel();
        $subscriptions = $model->getSubscriptions($year, $month);

        $monthName = MembershipTaxReport::monthToString($month);
        $filename = "moss_{$year}_$monthName.csv";

        header('Content-Type: text/csv');
        header('Cache-Control: no-cache');
        header('Content-Disposition: attachment; filename="'.$filename.'"');



        $fp = fopen('php://output', 'wb');

        //$headline = TaxCountry::getCSVHeader();
        // fputcsv($fp, $headline);
        $headline = "#v2.0\n";
        fputs($fp, $headline);


        /**
         * @var TaxCountry[] $countries
         */
        $countries = [];


        foreach($subscriptions as $subscription) {
            $this->addSubscription($countries, $subscription);
        }

        foreach($countries as $country) {
            fputcsv($fp, $country->getCSVLine_Satzart_1());
            fputcsv($fp, $country->getCSVLine_Satzart_2());
        }

        fclose($fp);

        die();
    }

    /**
     *
     * @param $countries TaxCountry[]
     * @param $subscription
     */
    private function addSubscription(&$countries, $subscription)
    {
        $countryCode = $subscription->country_2_code;

        if ($countryCode == 'GR') {
            $countryCode = 'EL';
        }

        if ($countryCode == 'DE') {
            return;
        }

        $netAmount = $subscription->amount;
        $taxAmount = $subscription->tax_amount;
        $taxRate = $subscription->tax_rate;
        $taxType = 'STANDARD';

        if (!isset($countries[$countryCode])) {
            $countries[$countryCode] = new TaxCountry($countryCode, $taxRate, $taxType);
        }

        $country = $countries[$countryCode];
        $country->addEntry($netAmount, $taxAmount);
    }
}

