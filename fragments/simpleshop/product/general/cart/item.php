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

$label_name       = sprogfield('name');
$product          = $this->getVar('product');
$class            = $this->getVar('class');
$has_rm_btn       = $this->getVar('has_remove_button', TRUE);
$has_image        = $this->getVar('has_image', TRUE);
$email_tpl_styles = $this->getVar('email_tpl_styles', []);
$image            = $product->getValue('image');
$price            = $product->getPrice(TRUE);
$features         = $product->getValue('features');
$quantity         = $product->getValue('cart_quantity');
$unit             = $product->getValue(sprogfield('unit'));
$key              = $product->getValue('key');
$product_url      = $product->getUrl();
$is_giftcard      = $product->getValue('type') == 'giftcard';
$extras           = $product->getValue('extras');
$has_qty_ctrl     = $is_giftcard ? FALSE : $this->getVar('has_quantity_control', TRUE);


$Category = Category::get($product->getValue('category_id'));

if ($has_image && strlen($image) == 0 && strlen($product->getValue('gallery')))
{
    $gallery = explode(',', $product->getValue('gallery'));
    $image   = $gallery[0];
}

?>
<tr class="cart-item <?= $class ?>" style="<?= $email_tpl_styles['tr'] ? $email_tpl_styles['tr'] : '' ?>">
    <?php if ($has_image): ?>
        <td class="product-image" style="<?= $email_tpl_styles['td'] ? $email_tpl_styles['td'] : '' ?>">
            <a href="<?= $product_url ?>"><?= Utils::getImageTag($image, 'cart-list-element-main') ?></a>
        </td>
    <?php endif; ?>
    <td class="description" style="<?= $email_tpl_styles['td'] ? $email_tpl_styles['td'] : '' ?>">
        <h3 style="<?= $email_tpl_styles['h3'] ? $email_tpl_styles['h3'] : '' ?>">
            <?= $product->getValue($label_name) ?>
        </h3>
        <?php if (!$is_giftcard): ?>
            <p style="<?= $email_tpl_styles['p'] ? $email_tpl_styles['p'] : '' ?>">
                <?= $Category->getValue($label_name) ?>
            </p>
        <?php elseif ($extras['coupon_code']): ?>
            <br/>
            <code style="<?= $email_tpl_styles['code'] ?>">
                Code: <?= $extras['coupon_code'] ?>
            </code>
        <?php endif; ?>
        <?php if (count($features)): ?>
            <?php foreach ($features as $feature): ?>
                <div class="feature"><?= $feature->getValue($label_name) ?></div>
            <?php endforeach; ?>
        <?php endif; ?>
    </td>
    <td class="price-single" style="<?= $email_tpl_styles['td'] ? $email_tpl_styles['td'] : '' ?>">
        &euro; <?= format_price($price) ?>
    </td>
    <td class="amount" style="<?= $email_tpl_styles['td'] ? $email_tpl_styles['td'] : '' ?>">
        <?php
        $this->setVar('has_quantity_control', $has_qty_ctrl);
        $this->setVar('has_refresh_button', $this->getVar('has_refresh_button', TRUE));
        $this->setVar('cart-quantity', $quantity);
        $this->setVar('product_key', $key);
        $this->setVar('unit', $unit);
        echo $this->subfragment('simpleshop/product/general/cart/button.php');
        ?>
    </td>
    <td class="price-total" style="<?= $email_tpl_styles['td'] ? $email_tpl_styles['td'] : '' ?>">
        &euro; <?= format_price($price * $quantity) ?>
    </td>
    <?php if ($has_rm_btn): ?>
        <td class="remove-product">
            <a href="<?= rex_getUrl(NULL, NULL, ['func' => 'remove', 'key' => $key]) ?>" class="remove">X</a>
        </td>
    <?php endif; ?>
</tr>