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

$quantity = $this->getVar('cart-quantity');
$product_key = $this->getVar('product_key');

if (!$quantity)
{
    $quantity = 1;
}
?>
<div class="clearfix">
    <?php if ($this->getVar('has_quantity_control')): ?>
        <div class="amount-increment clearfix">
            <button class="button minus">-</button>
            <input type="text" value="<?= $quantity ?>" name="quantity[<?= $product_key ?>]" readonly>
            <button class="button plus">+</button>
            <?php if ($this->getVar('has_refresh_button')): ?>
                <button class="button refresh" type="submit" name="func" value="update"><i class="fa fa-refresh" aria-hidden="true"></i></button>
            <?php endif; ?>
        </div>
    <?php endif; ?>
    <?php if ($this->getVar('has_add_to_cart_button')): ?>
    <a class="add-to-cart fbox" href="#shop-modal" data-product_key="<?= $product_key ?>" data-quantity="<?= $quantity ?>">
        <i class="fa fa-cart-plus" aria-hidden="true"></i>
        <span><?= $this->i18n('action.add_to_cart'); ?></span>
    </a>
    <?php endif; ?>
</div>