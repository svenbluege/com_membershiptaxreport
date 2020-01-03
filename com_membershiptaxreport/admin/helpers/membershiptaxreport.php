<?php

/**
 * @package     Sven.Bluege
 * @subpackage  com_membershiptaxreport
 *
 * @copyright   Copyright (C) 2005 - 2019 Sven Bluege All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;


abstract class MembershiptaxreportHelper
{

    /**
     * Translate the month into something readable.
     *
     * @param $month
     * @return int|string
     */
    public static function monthToString($month) {
        switch($month) {
            case 13: return 'Q1'; break;
            case 14: return 'Q2'; break;
            case 15: return 'Q3'; break;
            case 16: return 'Q4'; break;
            case 17: return 'Year'; break;
            default: $dateObj   = DateTime::createFromFormat('!m', $month); return $dateObj->format('F'); // March; break;
        }
    }

    /**
     * A month can be something between 1 and 17. 13-17 have special meanings.
     *
     * translates a given month into a comma separated list of month 1-12
     *
     * @param $month
     * @return int|string
     */
    public static function getMonthCondition($month) {
        switch($month) {
            case 13: return '1,2,3'; break;
            case 14: return '4,5,6'; break;
            case 15: return '7,8,9'; break;
            case 16: return '10,11,12'; break;
            case 17: return '1,2,3,4,5,6,7,8,9,10,11,12'; break;
            default: return (int) $month; break;
        }
    }
}
