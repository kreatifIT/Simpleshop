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

$Order     = $this->getVar('order');
$discounts = $this->getVar('discounts');
$tax       = $Order->getValue('tax');
$shipping  = $Order->getValue('shipping_costs');
$subtotal  = $Order->getValue('subtotal');
$total     = $Order->getValue('total');
$costs     = $subtotal + $shipping;

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
        <?php if ($tax > 0): ?>
        <div class="subtotal">
            <span>&euro; <?= format_price($tax) ?></span>
            <span>###label.tax_included###</span>
        </div>
        <?php endif; ?>
        <?php foreach ($discounts as $discount):
            $discount['value'] = $discount['value'] > $costs ? $costs : $discount['value'];
        ?>
            <div class="subtotal ">
                <span>&euro; -<?= format_price($discount['value']) ?></span>
                <span><?= $discount['name'] ?></span>
            </div>
        <?php endforeach; ?>
        <div class="subtotal total">
            <span>&euro; <?= format_price($total) ?></span>
            <span>###label.total_sum###</span>
        </div>
    </div>
</div>