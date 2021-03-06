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

$config       = $this->getVar('config', FragmentConfig::getValue('cart.button'));
$quantity     = $this->getVar('cart-quantity', 1);
$product_key  = $this->getVar('product_key');
$max_amount   = $this->getVar('max_amount', 1);
$prod_url     = $this->getVar('product_url', '#');
$prod_req_url = $this->getVar('product_request_url', '');

?>
<div class="quantity-ctrl-button" data-quantity-ctrl-button>
    <?php if ($config['has_quantity_control']): ?>
        <div class="amount-increment small" data-amount-increment>
            <button class="button" data-amount-increment-sign="minus" type="button"
                    onclick="Simpleshop.changeCartAmount(this, '<?= $config['has_add_to_cart_button'] ? '' : $product_key ?>', <?= (int)$max_amount ?>);">
                <i class="fas fa-minus-circle"></i>
            </button>
            <input type="text" class="amount-input" data-amount-input value="<?= $quantity ?>" name="quantity[<?= $product_key ?>]"
                   onblur="Simpleshop.changeCartAmount(this, '<?= $config['has_add_to_cart_button'] ? '' : $product_key ?>', <?= (int)$max_amount ?>);">
            <button class="button" data-amount-increment-sign="plus" type="button"
                    onclick="Simpleshop.changeCartAmount(this, '<?= $config['has_add_to_cart_button'] ? '' : $product_key ?>', <?= (int)$max_amount ?>);">
                <i class="fas fa-plus-circle"></i>
            </button>
        </div>
    <?php elseif ($config['has_quantity']): ?>
        <?= $quantity ?>
    <?php endif; ?>
    <?php if ($config['has_add_to_cart_button']): ?>
        <?php if ($config['is_disabled']): ?>
            <a class="add-to-cart disabled">
                <i class="fa fa-exclamation" aria-hidden="true"></i>
                <span>###label.product_not_available###</span>
            </a>
        <?php else: ?>
            <a class="add-to-cart <?= $config['css_class']['button'] ?>" href="javascript:;"
               onclick="Simpleshop.addToCart(this, '<?= $product_key ?>', <?= $config['has_quantity_control'] ? 0 : $quantity ?>, 'offcanvas_cart')">
                <i class="icon-add-shopping-cart"></i>
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