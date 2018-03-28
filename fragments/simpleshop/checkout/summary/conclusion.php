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
$shipping   = $Order->getValue('shipping_costs');
$subtotal   = $Order->getValue('brut_prices');
$total      = $Order->getValue('total');
$taxes      = $Order->getValue('taxes');
$promotions = (array) $Order->getValue('promotions');

?>
<div class="margin-bottom">
    <div class="row column">
        <div class="checkout-summary-total">
            <div class="subtotal">
                <span class="label">###simpleshop.brutto_total###</span>
                <span class="price">&euro;&nbsp;<?= format_price(array_sum($subtotal)) ?></span>
            </div>

            <?php if ($Order->getValue('shipping')): ?>
                <div class="shipping">
                    <span class="label">###simpleshop.shipping_costs###</span>
                    <span class="price">&euro;&nbsp;<?= format_price($shipping) ?></span>
                </div>
            <?php endif; ?>

            <?php foreach ($promotions as $promotion):
                $percent = $promotion->getValue('discount_percent');
                $_value = $promotion->getValue('discount_value');
                $value = $percent ? $percent . '%' : ($_value > $total ? $total : $_value);

                if ($value != 0):
                    ?>
                    <div class="promotions ">
                        <span>&euro;&nbsp;-<?= format_price($value) ?></span>
                        <span><?= $promotion->getName() ?></span>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>

            <?php if (!$Order->isTaxFree()): ?>
                <?php foreach ($taxes as $percent => $tax): ?>
                    <div class="taxes">
                        <span class="label"><?= $percent ?>% ###label.tax###</span>
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
</div>