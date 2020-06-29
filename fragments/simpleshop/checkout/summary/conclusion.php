<?php

/**
 * This file is part of the Simpleshop package.
 *
 * @author FriendsOfREDAXO
 * @author a.platter@kreatif.it
 * @author jan.kristinus@yakamara.de
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FriendsOfREDAXO\Simpleshop;

$Order      = $this->getVar('Order');
$total      = $Order->getValue('total');
$taxes      = $Order->getValue('taxes');
$shipping   = $Order->getValue('shipping');
$promotions = (array)$Order->getValue('promotions');
$isTaxFree  = $Order->isTaxFree();

?>
<div class="checkout-summary-total">
    <div class="subtotal">
        <span class="label">###label.subtotal###</span>
        <span class="price">&euro;&nbsp;<?= format_price($Order->getSubtotal(!$isTaxFree)) ?></span>
    </div>

    <?php foreach ($promotions as $promotion):
        if (!is_object($promotion) || $promotion->getValue('value') == 0) {
            continue;
        }
        ?>
        <div class="promotions ">
            <span class="label"><?= $promotion->getName() ?></span>
            <span class="price">&euro;&nbsp;-<?= format_price($promotion->getValue('value')) ?></span>
        </div>
    <?php endforeach; ?>

    <?php if ($shipping): ?>
        <div class="shipping">
            <span class="label">+ ###label.shipping_costs###</span>
            <span class="price">&euro;&nbsp;<?= format_price($shipping->getPrice($Order)) ?></span>
        </div>
    <?php endif; ?>

    <?php if (!$isTaxFree): ?>
        <?php
        $nettoTotal = $Order->getNettoTotal();

        if ($shipping) {
            $nettoTotal += $shipping->getNetPrice($Order);
        }
        $nettoTotal -= $Order->getDiscount(false);

        ?>
        <div class="gross-price">
            <span class="label">###label.gross_total###</span>
            <span class="price">&euro;&nbsp;<?= format_price($nettoTotal) ?></span>
        </div>

        <?php foreach ($taxes as $percent => $tax): ?>
            <div class="taxes">
                <span class="label">+ <?= $percent ?>% ###label.tax###</span>
                <span class="price">&euro;&nbsp;<?= format_price($tax) ?></span>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <div class="total">
        <span class="label">###label.total_sum###</span>
        <span class="price">&euro;&nbsp;<?= format_price($total) ?></span>
    </div>
</div>
