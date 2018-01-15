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

$config = array_merge([
    'has_quantity'           => true,
    'is_disabled'            => false,
    'has_refresh_button'     => false,
    'has_quantity_control'   => false,
    'has_add_to_cart_button' => false,
    'has_request_button'     => false,
    'has_detail_button'      => false,
    'has_qty_ctr_minus_attr' => '',
    'has_qty_ctr_plus_attr'  => '',
], $this->getVar('config', []));

$extras       = $this->getVar('extras', []);
$quantity     = $this->getVar('cart-quantity', 1);
$product_key  = $this->getVar('product_key');
$prod_url     = $this->getVar('product_url', '#');
$prod_req_url = $this->getVar('product_request_url', '');

?>
<div class="quantity-ctrl-button">
    <?php if ($config['has_quantity_control']): ?>
        <div class="amount-increment clearfix">
            <span class="button minus float-left" <?= $config['has_qty_ctr_minus_attr'] ?>>-</span>
            <input type="text" class="float-left" value="<?= $quantity ?>" name="quantity[<?= $product_key ?>]" readonly>
            <span class="button plus float-left" <?= $config['has_qty_ctr_plus_attr'] ?>>+</span>
            <?php if ($config['has_refresh_button']): ?>
                <button class="button secondary refresh float-left" type="submit" name="func" value="update"><i class="fa fa-refresh" aria-hidden="true"></i></button>
            <?php endif; ?>
        </div>
    <?php elseif ($config['has_quantity']): ?>
        <?= $quantity ?>
    <?php endif; ?>
    <?php if ($config['has_add_to_cart_button']): ?>
        <?php if ($config['is_disabled']): ?>
            <a class="add-to-cart disabled">
                <i class="fa fa-exclamation" aria-hidden="true"></i>
                <span>###shop.product_not_available###</span>
            </a>
        <?php else: ?>
            <a class="add-to-cart fbox" href="#shop-modal" data-product_key="<?= $product_key ?>"
               data-quantity="<?= $quantity ?>" <?php if (count($extras)): ?>data-extras="<?= htmlspecialchars(json_encode($extras), ENT_QUOTES, 'UTF-8'); ?><?php endif; ?>">
                <i class="fa fa-cart-plus" aria-hidden="true"></i>
                <span>###action.add_to_cart###</span>
            </a>
        <?php endif; ?>
    <?php elseif ($config['has_detail_button']): ?>
        <a class="button" href="<?= $prod_url ?>">
            <span>###label.details###</span>
        </a>
    <?php elseif ($config['has_request_button'] && strlen($prod_req_url)): ?>
        <a class="button" href="<?= $prod_req_url ?>">
            <span>###action.request_product###</span>
        </a>
    <?php endif; ?>
</div>