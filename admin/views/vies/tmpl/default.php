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

    .report {
        width: 100%;
    }

    tr:nth-child(odd) {background: #DDD}

    tr:nth-child(even) {background: #eee}

    tr.country-header {
        background-color: #CCC;
    }

    tr.country-header th {
        padding: 10px;
        font-size: 1.3em;
    }



    tr.subtotal,
    tr.header {
        background: black;
        color: white;
    }

    tr.total {
        background: darkblue;
        color: white;
    }
    tr.spacer {
        height: 2em;
        background-color: transparent;
    }

    td.amount,
    th.amount {
        text-align: right;
    }

</style>

<form action="<?php echo JRoute::_('index.php');?>" method="get">

    <select name="month">
        <?php FOR($i=1; $i<17; $i++) {
            $dateObj   = DateTime::createFromFormat('!m', $i);
            $monthName = $dateObj->format('F'); // March
            $selected = $this->month == $i ? 'selected="selected"': '';
            if ($i>12) {
                $monthName = 'Q' . ($i-12);
            }
            echo "<option value='$i' $selected>$monthName</option>";
        }?>
    </select>

    <select name="year">
        <?php FOR($i=2012; $i<=date("Y"); $i++) {
            $selected = $this->year == $i ? 'selected="selected"': '';
            echo "<option value='$i' $selected>$i</option>";
        }?>
    </select>

    <input type="submit" value="Load">
    <input type="hidden" value="com_membershiptaxreport" name="option">
    <input type="hidden" value="vies" name="view">

</form>

<h1>VIES Report for <?php echo ($this->month>12?'Q'.($this->month-12):$this->month) .'.'. $this->year; ?></h1>

<table class="report">

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
    $sumAmount = 0;
    $sumGrossAmount = 0;
    $sumTaxAmount = 0;
    $sumInvoices = 0;

    function renderFooterLine($cssClass, $sumAmount, $sumTaxAmount, $sumGrossAmount, $sumInvoices) {
        echo "<tr class='$cssClass'>";
        echo "<th align='left' colspan='4'>$sumInvoices Invoices</th><th class='amount'>$sumAmount</th><th></th><th class='amount'>$sumTaxAmount</th><th class='amount'>$sumGrossAmount</th></tr>";
        echo "</tr><tr class='spacer'><td></td></tr>";
    }
    ?>

    <?php FOREACH($this->subscriptions as $subscription):?>
        <?php

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
                <?php echo $subscription->tax; ?> %
            </td>
            <td class="amount">
                <?php echo $subscription->tax_amount; ?>
            </td>
            <td class="amount">
                <?php echo $subscription->gross_amount; ?>
            </td>
        </tr>

    <?php ENDFOREACH?>

    <?php renderFooterLine('total', $sumAmount, $sumTaxAmount, $sumGrossAmount, $sumInvoices); ?>

</table>

