<?php

/**
 * @package     Sven.Bluege
 * @subpackage  com_membershiptaxreport
 *
 * @copyright   Copyright (C) 2005 - 2019 Sven Bluege All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die('Restricted access');

?>
<style>

    td.amount,
    th.amount {
        text-align: right;
    }

</style>
<?php require_once(JPATH_COMPONENT_ADMINISTRATOR . '/src/Helper/GeoIPUpdate.php'); ?>

<form action="<?php echo \Joomla\CMS\Router\Route::_('index.php');?>" method="get">

    <div class="row g-1 mb-3">
        <div class="col">
            <select name="month" class="form-select">
                <?php FOR($i=1; $i<18; $i++) {
                    $selected = $this->month == $i ? 'selected="selected"': '';
                    $monthName = \Svenbluege\Component\MembershipProTaxReport\Administrator\Helper\MembershipTaxReport::monthToString($i);
                    echo "<option value='$i' $selected>$monthName</option>";
                }?>
            </select>
        </div>
        <div class="col">
            <select name="year" class="form-select">
                <?php FOR($i=2012; $i<=date("Y"); $i++) {
                    $selected = $this->year == $i ? 'selected="selected"': '';
                    echo "<option value='$i' $selected>$i</option>";
                }?>
            </select>
        </div>
        <div class="col">
            <input class="btn btn-primary" type="submit" value="Load">
        </div>
    </div>

    <input type="hidden" value="com_membershiptaxreport" name="option">
    <input type="hidden" value="all" name="view">

</form>

<h1>Report for <?php echo \Svenbluege\Component\MembershipProTaxReport\Administrator\Helper\MembershipTaxReport::monthToString($this->month) .'.'. $this->year; ?></h1>

<table class="report table table-hover table-striped">

    <tr class="header">
        <th>Invoice and date</th>
        <th>Issued to</th>
        <th>Payment Information</th>
        <th>VAT Nr</th>
        <th>Net Amount</th>
        <th>Tax Rate</th>
        <th>Tax Amount</th>
        <th>Payable Amount</th>
    </tr>

<?php
    $currentCountry = '';
    $sumCountryAmount = 0;
    $sumCountryGrossAmount = 0;
    $sumCountryTaxAmount = 0;
    $sumCountryInvoices = 0;

    $sumAmount = 0;
    $sumGrossAmount = 0;
    $sumTaxAmount = 0;
    $sumInvoices = 0;

    function renderFooterLine($cssClass, $sumAmount, $sumTaxAmount, $sumGrossAmount, $sumInvoices) {
        echo "<tr class='$cssClass table-primary'>";
        echo "<th align='left' colspan='4'>$sumInvoices Invoices</th><th class='amount'>$sumAmount</th><th></th><th class='amount'>$sumTaxAmount</th><th class='amount'>$sumGrossAmount</th></tr>";
        echo "</tr>";
    }
?>

<?php FOREACH($this->subscriptions as $subscription):?>
<?php

    $newCountry = $subscription->country_2_code;
    if ($newCountry != $currentCountry) {

        // render footer for country
        if ($currentCountry != '') {

            renderFooterLine('subtotal', $sumCountryAmount, $sumCountryTaxAmount, $sumCountryGrossAmount, $sumCountryInvoices);

        }

        $currentCountry = $newCountry;
        $sumCountryAmount = 0;
        $sumCountryGrossAmount = 0;
        $sumCountryTaxAmount = 0;
        $sumCountryInvoices = 0;

        echo "<tr class='country-header'>";
        echo "<th colspan='9'>";
        echo $subscription->country_2_code . ' - ' . $subscription->countryname;
        echo "</th>";
        echo "</tr>";

    }

    $sumCountryAmount += $subscription->amount;
    $sumCountryGrossAmount += $subscription->gross_amount;
    $sumCountryTaxAmount += $subscription->tax_amount;
    $sumCountryInvoices++;

    $sumAmount += $subscription->amount;
    $sumGrossAmount += $subscription->gross_amount;
    $sumTaxAmount += $subscription->tax_amount;
    $sumInvoices++;

    ?>

    <tr>
        <td>
            <strong>EG-<?php printf("%'.05d", $subscription->invoice_number); ?></strong><br>
            <?php echo $subscription->created_date; ?>
        </td>
        <td>
            <strong><?php echo $subscription->first_name; ?>
            <?php echo $subscription->last_name; ?></strong><br>
            <?php echo $subscription->address; ?> •
            <?php echo $subscription->zip; ?> •
            <?php echo $subscription->country; ?>
        </td>
        <td>
            <?php echo $subscription->payment_method; ?><br>
            <?php echo $subscription->transaction_id; ?>
        </td>
        <td>
            <?php IF (!empty($subscription->vat_number)):?>
                <?php echo $subscription->country_2_code . $subscription->vat_number; ?><br>
                <?php echo $subscription->vat_number; ?>
            <?php ENDIF; ?>
        </td>
        <td class="amount">
            <?php echo $subscription->amount; ?>
        </td>
        <td class="amount">
            <?php printf('%d',$subscription->tax_rate); ?> %
        </td>
        <td class="amount">
            <?php echo $subscription->tax_amount; ?>
        </td>
        <td class="amount">
            <?php echo $subscription->gross_amount; ?>
        </td>
    </tr>

<?php ENDFOREACH?>

<?php renderFooterLine('subtotal', $sumCountryAmount, $sumCountryTaxAmount, $sumCountryGrossAmount, $sumCountryInvoices); ?>
<?php renderFooterLine('total', $sumAmount, $sumTaxAmount, $sumGrossAmount, $sumInvoices); ?>

</table>
