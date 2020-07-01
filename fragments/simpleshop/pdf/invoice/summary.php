<?php

namespace FriendsOfREDAXO\Simpleshop;

$Order  = $this->getVar('Order');
$config = \FriendsOfREDAXO\Simpleshop\FragmentConfig::getValue('checkout');

$taxes      = $Order->getValue('taxes');
$shipping   = $Order->getValue('shipping');
$remarks    = trim($Order->getValue('remarks'));
$promotions = (array)$Order->getValue('promotions');
$isTaxFree  = $Order->isTaxFree();

?>
<?php if ($remarks != ''): ?>
    <div class="remarks">
        <strong>###label.remarks_infos###</strong><br>
        <p><?= nl2br($remarks) ?></p>
    </div>
<?php endif; ?>
<table id="invoice-summary" width="100%">

    <?php if ($Order->getValue('status') != 'CN'): ?>
        <tr>
            <td>
                ###label.subtotal###
            </td>
            <td align="right">
                &euro;&nbsp;<?= format_price($Order->getSubtotal(!$isTaxFree)) ?>
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
                    &euro;&nbsp;<?= format_price($Order->getShippingCosts()) ?>
                </td>
            </tr>
        <?php endif; ?>

        <?php if ($config['show_tax_info'] && !$Order->isTaxFree()): ?>
            <tr>
                <td>
                    ###label.gross_total###
                </td>
                <td align="right">
                    &euro;&nbsp;<?= format_price($Order->getNettoTotal(true)) ?>
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
            &euro;&nbsp;<?= format_price($Order->getTotal()) ?>
        </td>
    </tr>
</table>