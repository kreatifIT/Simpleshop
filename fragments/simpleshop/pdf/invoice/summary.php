<?php

$type           = $this->getVar('type', 'invoice');
$discounts      = $this->getVar('discounts', []);
$Order          = $this->getVar('Order');
$shipping_costs = $this->getVar('shipping_costs', null);
$initial_total  = $this->getVar('initial_total', 0);
$total          = $this->getVar('total', 0);
$tax            = $this->getVar('tax', null);
$taxes          = array_filter((array) $Order->getValue('taxes', []));
$promotions     = (array) $Order->getValue('promotions', []);

if ($tax == null) {
    $tax = $Order->getValue('tax', null);
}


// old data structure (in case no promo value exists)
if (count($Order->getValue('abos')) && (empty($promotions) || (isset($promotions['manual_discount']) && count($promotions) == 1))) {

    foreach ($Order->getValue('abos') as $abo) {
        $total -= $abo['value'];
    }
    if (count($taxes)) {
        $tax = 0;

        foreach ($taxes as $taxPerc => &$taxVal) {
            $taxVal = $total / (100 + $taxPerc) * $taxPerc;
            $tax    += $taxVal;
        }
    }
    else {
        $tax = $total / (122) * 22;
    }
}

?>
<table id="invoice-summary" width="100%">

    <?php if ($Order->getValue('status') != 'CN'): ?>
        <?php if ($initial_total > 0): ?>
            <tr>
                <td>###label.subtotal###</td>
                <td align="right">
                    &euro; <?= format_price($initial_total) ?>
                </td>
            </tr>
        <?php endif; ?>

        <?php if ($shipping_costs !== null): ?>
            <tr>
                <td>###label.shipment_cost###</td>
                <td align="right">
                    &euro; <?= format_price($shipping_costs) ?>
                </td>
            </tr>
        <?php endif; ?>

        <?php foreach ($discounts as $discount): ?>
            <tr>
                <td><?= $discount['name'] ?></td>
                <td align="right">
                    &euro; -<?= format_price($discount['value']) ?>
                </td>
            </tr>
        <?php endforeach; ?>

        <?php if ($type == 'invoice'): ?>
            <!-- subtotal -->
            <tr>
                <td>###label.gross_price###</td>
                <td align="right">
                    &euro; <?= format_price($total - $tax) ?>
                </td>
            </tr>

            <?php if (count($taxes)): ?>
                <?php foreach ($taxes as $_tax_perc => $_tax): ?>
                    <tr>
                        <td>###label.tax### <?= $_tax_perc ?>%</td>
                        <td align="right">
                            &euro; <?= format_price($_tax) ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php elseif ($tax !== null): ?>
                <tr>
                    <td>###label.tax_included### 22%</td>
                    <td align="right">
                        &euro; <?= format_price($tax) ?>
                    </td>
                </tr>
            <?php endif; ?>
        <?php endif; ?>
    <?php endif; ?>

    <!-- total -->
    <tr>
        <td id="invoice-summary-total-label" <?= $Order->getValue('status') == 'CN' ? 'style="padding-top:30mm;"' : '' ?>>###label.total_sum###</td>
        <td id="invoice-summary-total-value" align="right" valign="bottom">
            &euro; <?= format_price($total) ?>
        </td>
    </tr>
</table>