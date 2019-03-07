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

$Order       = $this->getVar('Order');
$shipping    = $Order->getValue('shipping_costs');
$brut_prices = $Order->getValue('brut_prices');
$discount    = $Order->getValue('discount');
$total       = $Order->getValue('total');
$taxes       = $Order->getValue('taxes');
$promotions  = (array)$Order->getValue('promotions');

?>
<div class="margin-bottom">
    <div class="checkout-summary-total">
        <div class="subtotal">
            <span class="label">###simpleshop.subtotal###</span>
            <span class="price">&euro;&nbsp;<?= format_price(array_sum($brut_prices)) ?></span>
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

        <?php if ($Order->getValue('shipping')): ?>
            <div class="shipping">
                <span class="label">+ ###simpleshop.shipping_costs###</span>
                <span class="price">&euro;&nbsp;<?= format_price($shipping) ?></span>
            </div>
        <?php endif; ?>

        <?php if (!$Order->isTaxFree()): ?>
            <div class="gross-price margin-small-top">
                <span class="label">###simpleshop.brutto_total###</span>
                <span class="price">&euro;&nbsp;<?= format_price(array_sum($brut_prices) + $shipping - $discount) ?></span>
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
</div>