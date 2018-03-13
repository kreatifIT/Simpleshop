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
$shipping   = $Order->getValue('initial_shipping_costs');
$subtotal   = $Order->getValue('initial_total');
$total      = $Order->getValue('total');
$taxes      = $Order->getValue('taxes');
$taxTotal   = $Order->getTaxTotal();
$promotions = (array) $Order->getValue('promotions');

?>
<div class="order-total margin-bottom">
    <div class="row column text-right">
        <?php if ($subtotal != $total): ?>
            <div class="subtotal">
                ###label.subtotal###
                <span class="price">&euro;&nbsp;<?= format_price($subtotal) ?></span>
            </div>
        <?php endif; ?>

        <?php if ($shipping > 0): ?>
            <div class="shipping">
                ###label.shipment_cost###
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

        <?php foreach ($taxes as $percent => $tax): ?>
            <div class="taxes">
                ###label.tax_included### <?= $percent ?>%
                <span class="price">&euro;&nbsp;<?= format_price($tax) ?></span>
            </div>
        <?php endforeach; ?>
        <div class="total">
            ###label.total_sum###
            <span class="price">&euro;&nbsp;<?= format_price($total) ?></span>
        </div>
    </div>
</div>