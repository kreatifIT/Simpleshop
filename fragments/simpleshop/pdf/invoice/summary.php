<?php

namespace FriendsOfREDAXO\Simpleshop;

$Order          = $this->getVar('Order');
$config         = \FriendsOfREDAXO\Simpleshop\FragmentConfig::getValue('checkout');

$discount       = $Order->getValue('discount');
$total          = $Order->getValue('total');
$taxes          = $Order->getValue('taxes');
$shipping       = $Order->getValue('shipping');
$promotions     = (array)$Order->getValue('promotions');
$shipping_costs = $shipping && $config['show_tax_info'] ? $shipping->getNetPrice($Order) : $Order->getValue('shipping_costs');
$summary        = $Order->getValue($config['show_tax_info'] ? 'net_prices' : 'brut_prices');

?>
<table id="invoice-summary" width="100%">

    <?php if ($Order->getValue('status') != 'CN'): ?>
        <tr>
            <td>
                ###label.subtotal###
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

        <?php if ($shipping): ?>
            <tr>
                <td>
                    + ###label.shipping_costs###
                </td>
                <td align="right">
                    &euro;&nbsp;<?= format_price($shipping_costs) ?>
                </td>
            </tr>
        <?php endif; ?>

        <?php if ($config['show_tax_info'] && !$Order->isTaxFree()): ?>
            <tr>
                <td>
                    ###label.gross_total###
                </td>
                <td align="right">
                    &euro;&nbsp;<?= format_price(array_sum($summary) + $shipping_costs - $discount) ?>
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