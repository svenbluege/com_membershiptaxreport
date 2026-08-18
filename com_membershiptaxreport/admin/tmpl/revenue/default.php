<?php

/**
 * @package     Sven.Bluege
 * @subpackage  com_membershiptaxreport
 *
 * @copyright   Copyright (C) 2005 - 2019 Sven Bluege All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Router\Route;
use Svenbluege\Component\MembershipProTaxReport\Administrator\Helper\MembershipTaxReport;
use Svenbluege\Component\MembershipProTaxReport\Administrator\View\Revenue\HtmlView;

$statistics = $this->statistics;
$metrics    = HtmlView::$metrics;
$metricName = $metrics[$this->metric];
$metric     = $this->metric;

// no year selected means: show all years side by side
$singleYear = $this->year > 0;

$noFigures                = new stdClass();
$noFigures->subscriptions = 0;
$noFigures->net_amount    = 0.0;
$noFigures->tax_amount    = 0.0;
$noFigures->gross_amount  = 0.0;

$formatAmount = function($value) {
    return number_format((float)$value, 2);
};

/**
 * Renders a figure of the selected metric. Amounts are rounded to keep the matrix readable.
 */
$formatMetric = function($value) {
    if ($value == 0) {
        return '<span class="text-muted">&ndash;</span>';
    }
    return number_format((float)$value, 0);
};

/**
 * Renders a figure of the selected metric with all its decimals.
 */
$formatExact = function($value) use ($metric) {
    return number_format((float)$value, $metric === "subscriptions" ? 0 : 2);
};

/**
 * Renders the change of the selected metric compared to the previous period.
 */
$formatChange = function($figures, $previousFigures) use ($metric) {
    if ($previousFigures === null || $previousFigures->$metric == 0) {
        return '<span class="text-muted">&ndash;</span>';
    }

    $change   = ($figures->$metric - $previousFigures->$metric) / abs($previousFigures->$metric) * 100;
    $cssClass = $change >= 0 ? 'text-success' : 'text-danger';

    return sprintf('<span class="%s">%+.1f %%</span>', $cssClass, $change);
};

$renderBar = function($value, $maxValue) {
    $width = $maxValue > 0 ? max(0, $value) / $maxValue * 100 : 0;
    return sprintf('<div class="revenue-bar"><span style="width: %.2f%%"></span></div>', $width);
};

?>
<style>

    td.amount,
    th.amount {
        text-align: right;
        white-space: nowrap;
    }

    .revenue-bar {
        background: rgba(127, 127, 127, .2);
        border-radius: 2px;
        height: 10px;
        min-width: 80px;
    }

    .revenue-bar span {
        background: var(--link-color, #0d6efd);
        border-radius: 2px;
        display: block;
        height: 100%;
    }

    .revenue-kpi {
        font-size: 1.5rem;
        font-weight: 600;
    }

    @media (prefers-color-scheme: dark) {
        .total {
            --body-color: black;
            color: black;
        }
    }

</style>

<form action="<?php echo Route::_('index.php');?>" method="get">

    <div class="row g-1 mb-3">
        <div class="col">
            <select name="year" class="form-select">
                <option value="0" <?php echo $singleYear ? '' : 'selected="selected"'; ?>>All years</option>
                <?php FOREACH(array_reverse($statistics->years) as $year) {
                    $selected = $this->year == $year ? 'selected="selected"': '';
                    echo "<option value='$year' $selected>$year</option>";
                }?>
            </select>
        </div>
        <div class="col">
            <select name="metric" class="form-select">
                <?php FOREACH($metrics as $key => $title) {
                    $selected = $metric == $key ? 'selected="selected"': '';
                    echo "<option value='$key' $selected>$title</option>";
                }?>
            </select>
        </div>
        <div class="col">
            <input class="btn btn-primary" type="submit" value="Load">
        </div>
    </div>

    <input type="hidden" value="com_membershiptaxreport" name="option">
    <input type="hidden" value="revenue" name="view">

</form>

<h1>Revenue statistics <?php echo $singleYear ? 'for ' . $this->year : 'for all years'; ?></h1>

<?php IF (empty($statistics->years)): ?>
    <div class="alert alert-info">There are no subscriptions to report.</div>
    <?php return; ?>
<?php ENDIF; ?>

<?php
    $scope = $singleYear ? $statistics->yearTotals[$this->year] : $statistics->total;

    // the rows of the detail table: either the months of the selected year or all years
    $rows = [];
    IF ($singleYear) {
        FOR($month = 1; $month <= 12; $month++) {
            $rows[] = [
                'label'    => MembershipTaxReport::monthToString($month),
                'figures'  => $statistics->months[$this->year][$month] ?? $noFigures,
                'previous' => $statistics->months[$this->year - 1][$month] ?? null
            ];
        }
    } ELSE {
        FOREACH($statistics->years as $year) {
            $rows[] = [
                'label'    => $year,
                'figures'  => $statistics->yearTotals[$year],
                'previous' => $statistics->yearTotals[$year - 1] ?? null
            ];
        }
    }

    $maxValue = 0;
    FOREACH($rows as $row) {
        $maxValue = max($maxValue, $row['figures']->$metric);
    }
?>

<div class="row g-2 mb-4">
    <div class="col-md-3">
        <div class="card h-100">
            <div class="card-body">
                <div class="small text-muted">Net Amount</div>
                <div class="revenue-kpi"><?php echo $formatAmount($scope->net_amount); ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card h-100">
            <div class="card-body">
                <div class="small text-muted">Tax Amount</div>
                <div class="revenue-kpi"><?php echo $formatAmount($scope->tax_amount); ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card h-100">
            <div class="card-body">
                <div class="small text-muted">Payable Amount</div>
                <div class="revenue-kpi"><?php echo $formatAmount($scope->gross_amount); ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card h-100">
            <div class="card-body">
                <div class="small text-muted">Subscriptions</div>
                <div class="revenue-kpi"><?php echo number_format($scope->subscriptions); ?></div>
                <div class="small text-muted">
                    &#216; <?php echo $formatAmount($scope->subscriptions > 0 ? $scope->net_amount / $scope->subscriptions : 0); ?> net
                </div>
            </div>
        </div>
    </div>
</div>

<table class="report table table-hover table-striped">

    <tr class="header">
        <th><?php echo $singleYear ? 'Month' : 'Year'; ?></th>
        <th class="amount">Subscriptions</th>
        <th class="amount">Net Amount</th>
        <th class="amount">Tax Amount</th>
        <th class="amount">Payable Amount</th>
        <th class="amount">&#216; Net Amount</th>
        <th class="amount" title="<?php echo $metricName; ?> compared to the same period one year earlier">
            <?php echo $metricName; ?> +/-
        </th>
        <th style="width: 20%"><?php echo $metricName; ?></th>
    </tr>

<?php FOREACH($rows as $row): ?>
<?php $figures = $row['figures']; ?>
    <tr<?php echo $figures->subscriptions == 0 ? ' class="text-muted"' : ''; ?>>
        <td>
            <strong><?php echo $row['label']; ?></strong>
        </td>
        <td class="amount">
            <?php echo number_format($figures->subscriptions); ?>
        </td>
        <td class="amount">
            <?php echo $formatAmount($figures->net_amount); ?>
        </td>
        <td class="amount">
            <?php echo $formatAmount($figures->tax_amount); ?>
        </td>
        <td class="amount">
            <?php echo $formatAmount($figures->gross_amount); ?>
        </td>
        <td class="amount">
            <?php echo $figures->subscriptions > 0 ? $formatAmount($figures->net_amount / $figures->subscriptions) : ''; ?>
        </td>
        <td class="amount">
            <?php echo $formatChange($figures, $row['previous']); ?>
        </td>
        <td>
            <?php echo $renderBar($figures->$metric, $maxValue); ?>
        </td>
    </tr>
<?php ENDFOREACH; ?>

    <tr class="total table-primary">
        <th>Total</th>
        <th class="amount"><?php echo number_format($scope->subscriptions); ?></th>
        <th class="amount"><?php echo $formatAmount($scope->net_amount); ?></th>
        <th class="amount"><?php echo $formatAmount($scope->tax_amount); ?></th>
        <th class="amount"><?php echo $formatAmount($scope->gross_amount); ?></th>
        <th class="amount"><?php echo $formatAmount($scope->subscriptions > 0 ? $scope->net_amount / $scope->subscriptions : 0); ?></th>
        <th></th>
        <th></th>
    </tr>

</table>

<h2><?php echo $metricName; ?> per month <small class="text-muted">(amounts are rounded)</small></h2>

<table class="report table table-hover table-striped">

    <tr class="header">
        <th>Year</th>
        <?php FOR($month = 1; $month <= 12; $month++): ?>
            <th class="amount"><?php echo substr(MembershipTaxReport::monthToString($month), 0, 3); ?></th>
        <?php ENDFOR; ?>
        <th class="amount">Total</th>
    </tr>

    <?php FOREACH(array_reverse($statistics->years) as $year): ?>
        <tr<?php echo $this->year == $year ? ' class="table-active"' : ''; ?>>
            <th>
                <a href="<?php echo Route::_('index.php?option=com_membershiptaxreport&view=revenue&metric=' . $metric . '&year=' . $year); ?>"><?php echo $year; ?></a>
            </th>
            <?php FOR($month = 1; $month <= 12; $month++): ?>
                <?php $figures = $statistics->months[$year][$month] ?? $noFigures; ?>
                <td class="amount" title="<?php echo $formatExact($figures->$metric); ?>">
                    <?php echo $formatMetric($figures->$metric); ?>
                </td>
            <?php ENDFOR; ?>
            <td class="amount">
                <strong><?php echo $formatMetric($statistics->yearTotals[$year]->$metric); ?></strong>
            </td>
        </tr>
    <?php ENDFOREACH; ?>

    <tr class="total table-primary">
        <th>Total</th>
        <?php FOR($month = 1; $month <= 12; $month++): ?>
            <th class="amount"><?php echo $formatMetric($statistics->monthTotals[$month]->$metric); ?></th>
        <?php ENDFOR; ?>
        <th class="amount"><?php echo $formatMetric($statistics->total->$metric); ?></th>
    </tr>

</table>
