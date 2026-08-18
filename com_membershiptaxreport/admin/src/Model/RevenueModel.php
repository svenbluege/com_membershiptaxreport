<?php
namespace Svenbluege\Component\MembershipProTaxReport\Administrator\Model;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;

/**
 * @package     Sven.Bluege
 * @subpackage  com_membershiptaxreport
 *
 * @copyright   Copyright (C) 2005 - 2019 Sven Bluege All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

class RevenueModel extends BaseDatabaseModel
{

    /**
     * Aggregates all paid subscriptions per year and month.
     *
     * No join is used here on purpose. Joining the custom field values like the other
     * reports do would multiply the rows of a subscription and break every sum.
     *
     * @return object[] one row per year/month combination which has subscriptions
     */
    protected function getMonthlyRows() {
        $db     = Factory::getDbo();
        $query  = $db->getQuery(true)
            ->select('YEAR(s.created_date) as year, MONTH(s.created_date) as month')
            ->select('COUNT(*) as subscriptions')
            ->select('SUM(s.amount-s.discount_amount) as net_amount')
            ->select('SUM(s.tax_amount) as tax_amount')
            ->select('SUM(s.gross_amount) as gross_amount')
            ->from('#__osmembership_subscribers s')
            ->where('(s.published = 1 OR s.published = 2)')
            ->group('YEAR(s.created_date), MONTH(s.created_date)')
            ->order('year, month');

        $db->setQuery($query);
        return $db->loadObjectList();
    }

    /**
     * Revenue statistics of all subscriptions in the system, broken down by month.
     *
     * @return object with these properties
     *                years       int[]                 all years which have subscriptions, ascending
     *                months      object[year][month]   the figures of a single month
     *                yearTotals  object[year]          the figures of a whole year
     *                monthTotals object[month]         the figures of a month across all years
     *                total       object                the figures of all years
     */
    public function getStatistics() {

        $statistics              = new \stdClass();
        $statistics->years       = [];
        $statistics->months      = [];
        $statistics->yearTotals  = [];
        $statistics->monthTotals = [];
        $statistics->total       = $this->createFigures();

        for($month = 1; $month <= 12; $month++) {
            $statistics->monthTotals[$month] = $this->createFigures();
        }

        foreach($this->getMonthlyRows() as $row) {

            $year  = (int) $row->year;
            $month = (int) $row->month;

            // a subscription without a proper creation date cannot be assigned to a month
            if ($year == 0 || $month == 0) {
                continue;
            }

            if (!isset($statistics->months[$year])) {
                $statistics->years[]            = $year;
                $statistics->months[$year]      = [];
                $statistics->yearTotals[$year]  = $this->createFigures();
            }

            $figures = $this->createFigures();
            $figures->subscriptions = (int) $row->subscriptions;
            $figures->net_amount    = (float) $row->net_amount;
            $figures->tax_amount    = (float) $row->tax_amount;
            $figures->gross_amount  = (float) $row->gross_amount;

            $statistics->months[$year][$month] = $figures;

            $this->addFigures($statistics->yearTotals[$year], $figures);
            $this->addFigures($statistics->monthTotals[$month], $figures);
            $this->addFigures($statistics->total, $figures);
        }

        return $statistics;
    }

    /**
     * @return object an empty set of figures
     */
    private function createFigures() {
        $figures                = new \stdClass();
        $figures->subscriptions = 0;
        $figures->net_amount    = 0.0;
        $figures->tax_amount    = 0.0;
        $figures->gross_amount  = 0.0;

        return $figures;
    }

    /**
     * @param $target object the figures to add to
     * @param $figures object the figures to add
     */
    private function addFigures($target, $figures) {
        $target->subscriptions += $figures->subscriptions;
        $target->net_amount    += $figures->net_amount;
        $target->tax_amount    += $figures->tax_amount;
        $target->gross_amount  += $figures->gross_amount;
    }

}
