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

$Order      = $this->getVar('order');
$taxes      = $Order->getTaxTotal();
$shipping   = $Order->getValue('initial_shipping_costs');
$subtotal   = $Order->getValue('initial_total');
$total      = $Order->getValue('total');
$promotions = $Order->getValue('promotions');

?>
<div class="row column">
    <div class="order-total">
        <?php if ($subtotal != $total): ?>
            <div class="subtotal">
                <span>&euro; <?= format_price($subtotal) ?></span>
                <span>###label.subtotal###</span>
            </div>
        <?php endif; ?>
        <?php if ($shipping > 0): ?>
            <div class="subtotal ">
                <span>&euro; <?= format_price($shipping) ?></span>
                <span>###label.shipment_cost###</span>
            </div>
        <?php endif; ?>
        <?php foreach ($promotions as $promotion):
            $percent = $promotion->getValue('discount_percent');
            $_value = $promotion->getValue('discount_value');
            $value = $percent ? $percent . '%' : ($_value > $total ? $total : $_value);

            if ($value != 0):
                ?>
                <div class="subtotal ">
                    <span>&euro; -<?= format_price($value) ?></span>
                    <span><?= $promotion->getName() ?></span>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>
        <?php foreach ($taxes as $percent => $tax): ?>
            <div class="subtotal">
                <span>&euro; <?= format_price($tax) ?></span>
                <span>###label.tax_included### <?= $percent ?>%</span>
            </div>
        <?php endforeach; ?>
        <div class="subtotal total">
            <span>&euro; <?= format_price($total) ?></span>
            <span>###label.total_sum###</span>
        </div>
    </div>
</div>