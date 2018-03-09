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

$product = $this->getVar('product');

$images      = $product->getArrayValue('pictures');
$picture     = $images[0];
$price       = $product->getPrice(true);
$features    = $product->getValue('features');
$quantity    = $product->getValue('cart_quantity');
$key         = $product->getValue('key');
$product_url = $product->getUrl();
$is_giftcard = $product->getValue('type') == 'giftcard';
$extras      = $product->getValue('extras');

$Category = $product->valueIsset('category_id') ? Category::get($product->getValue('category_id')) : null;

$config = FragmentConfig::getValue('cart');
$styles = FragmentConfig::getValue('styles');

if ($is_giftcard) {
    $config['has_quantity_control'] = false;
}

?>
<tr class="cart-item" <?= $styles['tr'] ?>>
    <?php if ($config['has_image']): ?>
        <td class="product-image" <?= $styles['td'] ?>>
            <a href="<?= $product_url ?>"><?= Utils::getImageTag($picture, 'cart-list-element-main') ?></a>
        </td>
    <?php endif; ?>
    <td class="description" <?= $styles['td'] ?>>
        <h3 <?= $styles['h2'] ?>>
            <?= $product->getName() ?>
        </h3>
        <?php if ($Category && !$is_giftcard): ?>
            <p <?= $styles['p'] ?>>
                <?= $Category->getName() ?>
            </p>
        <?php elseif ($extras['coupon_code']): ?>
            <br/>
            <code <?= $styles['code'] ?>>
                Code: <?= $extras['coupon_code'] ?>
            </code>
        <?php endif; ?>
        <?php if (count($features)): ?>
            <?php foreach ($features as $feature): ?>
                <div class="feature"><?= $feature->getName() ?></div>
            <?php endforeach; ?>
        <?php endif; ?>
    </td>
    <td class="price-single" <?= $styles['td'] ?>>
        &euro; <?= format_price($price) ?>
    </td>
    <td class="amount" <?= $styles['td'] ?>>
        <?php
        $fragment = new \rex_fragment();
        $fragment->setVar('cart-quantity', $quantity);
        $fragment->setVar('product_key', $key);
        echo $fragment->parse('simpleshop/cart/button.php');
        ?>
    </td>
    <td class="price-total" <?= $styles['td'] ?>>
        &euro; <?= format_price($price * $quantity) ?>
    </td>
    <?php if ($config['has_remove_button']): ?>
        <td class="remove-product">
            <a href="<?= rex_getUrl(null, null, ['func' => 'remove', 'key' => $key]) ?>" class="remove">X</a>
        </td>
    <?php endif; ?>
</tr>