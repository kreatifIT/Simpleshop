<?php

namespace FriendsOfREDAXO\Simpleshop;

$type        = $this->getVar('type', 'invoice');
$Order       = $this->getVar('Order');
$shipping    = $Order->getValue('shipping_costs');
$summary     = $Order->getValue($type == 'invoice' ? 'brut_prices' : 'net_prices');
$discount    = $Order->getValue('discount');
$total       = $Order->getValue('total');
$taxes       = $Order->getValue('taxes');
$promotions  = (array)$Order->getValue('promotions');

if ($type != 'invoice' && $Order->getValue('shipping')) {
    $shipping = (float) $Order->getValue('shipping')->getNetPrice($Order);
}

?>
<table id="invoice-summary" width="100%">

    <?php if ($Order->getValue('status') != 'CN'): ?>
        <tr>
            <td>
                ###simpleshop.subtotal###
            </td>
            <td align="right">
                &euro;&nbsp;<?= format_price(array_sum($summary)) ?>
            </td>
        </tr>

        <?php foreach ($promotions as $promotion):
            if (!is_object($promotion) || $promotion->getValue('value') == 0) {
                continue;
            }
            ?>
            <tr>
                <td>
                    <?= $promotion->getName() ?>
                </td>
                <td align="right">
                    &euro;&nbsp;-<?= format_price($promotion->getValue('value')) ?>
                </td>
            </tr>
        <?php endforeach; ?>

        <?php if ($Order->getValue('shipping')): ?>
            <tr>
                <td>
                    + ###simpleshop.shipping_costs###
                </td>
                <td align="right">
                    &euro;&nbsp;<?= format_price($shipping) ?>
                </td>
            </tr>
        <?php endif; ?>

        <?php if ($type == 'invoice' && !$Order->isTaxFree()): ?>
            <tr>
                <td>
                    ###simpleshop.brutto_total###
                </td>
                <td align="right">
                    &euro;&nbsp;<?= format_price(array_sum($summary) + $shipping - $discount) ?>
                </td>
            </tr>

            <?php foreach ($taxes as $percent => $tax): ?>
                <tr>
                    <td>
                        + <?= $percent ?>% ###label.tax###
                    </td>
                    <td align="right">
                        &euro;&nbsp;<?= format_price($tax) ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>

    <?php endif; ?>

    <!-- total -->
    <tr>
        <td id="invoice-summary-total-label" <?= $Order->getValue('status') == 'CN' ? 'style="padding-top:30mm;"' : '' ?>>###label.total_sum###</td>
        <td id="invoice-summary-total-value" align="right" valign="bottom">
            &euro;&nbsp;<?= format_price($total) ?>
        </td>
    </tr>
</table>