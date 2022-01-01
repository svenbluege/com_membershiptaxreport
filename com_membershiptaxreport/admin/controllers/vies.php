<?php

/**
 * @package     Sven.Bluege
 * @subpackage  com_membershiptaxreport
 *
 * @copyright   Copyright (C) 2005 - 2019 Sven Bluege All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die('Restricted access');

class MembershiptaxreportControllerVies extends JControllerForm
{
    public function getModel($name = 'Vies', $prefix ='MembershiptaxreportModel', $config = array('ignore_request' => true))
    {
        return parent::getModel($name, $prefix, $config);
    }

    public function export()
    {
        /**
         * @var MembershiptaxreportModelMoss $model
         */

        $app = JFactory::getApplication();
        $year = $app->input->getInt('year');
        $month = $app->input->getInt('month');
        $debugMode = $app->input->getBool('debug', false);

        $model = $this->getModel();
        $subscriptions = $model->getSubscriptions($year, $month);

        $monthName = MembershiptaxreportHelper::monthToString($month);
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

class ViesEntry {
    private $countryCode;
    private $vatNumber;
    private $type = 'L';
    private $netAmount;


    public function __construct($countryCode, $vatNumber, $netAmount)
    {

        if ($countryCode == 'GR') {
            $countryCode = 'EL';
        }

        $this->countryCode = $countryCode;
        $this->vatNumber = $vatNumber;
        $this->netAmount = $netAmount;
    }

    public function addNetAmount(int $netAmount) {
        $this->netAmount += $netAmount;
    }

    public static function getCSVHeader() {
        return [
            'Länderkennzeichen',
            'USt-IdNr.',
            'Betrag (Euro)',
            'Art der Leistung'
        ];
    }

    public function getCSVLine() {
        return [
            $this->countryCode,
            $this->vatNumber,
            (int)round($this->netAmount),
            $this->type
        ];
    }
}
