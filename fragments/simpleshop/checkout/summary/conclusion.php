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

?>
<div class="row column">
    <div class="order-total">
        <div class="subtotal">
            <span>&euro; <?= format_price($Order->getValue('tax')) ?></span>
            <span>###label.tax_included###</span>
        </div>
        <div class="subtotal">
            <span>&euro; <?= format_price($Order->getValue('subtotal')) ?></span>
            <span>###label.subtotal###</span>
        </div>
        <div class="subtotal ">
            <span>&euro; <?= format_price($Order->getValue('shipping_costs')) ?></span>
            <span>###label.shipment_cost###</span>
        </div>
        <?php foreach ($discounts as $discount): ?>
            <div class="subtotal ">
                <span>&euro; -<?= format_price($discount['value']) ?></span>
                <span><?= $discount['name'] ?></span>
            </div>
        <?php endforeach; ?>
        <div class="subtotal total">
            <span>&euro; <?= format_price($Order->getValue('total')) ?></span>
            <span>###label.total_sum###</span>
        </div>
    </div>
</div>