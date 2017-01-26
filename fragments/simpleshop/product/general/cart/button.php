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

$is_disabled            = $this->getVar('is_disabled', FALSE);
$quantity               = $this->getVar('cart-quantity');
$product_key            = $this->getVar('product_key');
$extras                 = $this->getVar('extras', []);
$has_quantity           = $this->getVar('has_quantity', TRUE);
$unit                   = $this->getVar('unit', '');
$has_quantity_ctrl      = $this->getVar('has_quantity_control', FALSE);
$has_add_to_cart_button = $this->getVar('has_add_to_cart_button', FALSE);
$has_refresh_button     = $this->getVar('has_refresh_button', FALSE);

if (!$quantity)
{
    $quantity = 1;
}
?>
<div class="clearfix">
    <?php if ($has_quantity_ctrl): ?>
        <div class="amount-increment clearfix">
            <span class="button minus">-</span>
            <input type="text" value="<?= $quantity ?>" name="quantity[<?= $product_key ?>]" readonly>
            <span class="button plus">+</span>
            <?php if ($has_refresh_button): ?>
                <button class="button refresh" type="submit" name="func" value="update"><i class="fa fa-refresh" aria-hidden="true"></i></button>
            <?php endif; ?>
        </div>
    <?php elseif ($has_quantity): ?>
        <?= $quantity ?> <?= $unit ?>
    <?php endif; ?>
    <?php if ($has_add_to_cart_button): ?>
        <?php if ($is_disabled): ?>
            <a class="add-to-cart disabled">
                <i class="fa fa-exclamation" aria-hidden="true"></i>
                <span>###shop.product_not_available###</span>
            </a>
        <?php else: ?>
            <a class="add-to-cart fbox" href="#shop-modal" data-product_key="<?= $product_key ?>"
               data-quantity="<?= $quantity ?>" data-extras="<?= htmlspecialchars(json_encode($extras), ENT_QUOTES, 'UTF-8'); ?>">
                <i class="fa fa-cart-plus" aria-hidden="true"></i>
                <span>###action.add_to_cart###</span>
            </a>
        <?php endif; ?>
    <?php endif; ?>
</div>