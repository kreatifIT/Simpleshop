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

use Kreatif\Resource;


$product      = $this->getVar('product');
$useTaxPrices = $this->getVar('use_tax_prices', true);

$images      = $product->getArrayValue('images');
$picture     = array_shift($images);
$price       = $product->getPrice($useTaxPrices);
$features    = $product->getValue('features');
$quantity    = $product->getValue('cart_quantity');
$key         = $product->getValue('key');
$product_url = $product->getUrl();
$is_giftcard = $product->getValue('type') == 'giftcard';
$extras      = $product->getValue('extras');
$discount    = $product->getValue('discount');

$category = $product->valueIsset('category') ? \rex_article::get($product->getValue('category')) : null;

$config = $this->getVar('cart_config', FragmentConfig::getValue('cart'));
$styles = FragmentConfig::getValue('styles');

if ($is_giftcard) {
    $config['has_quantity_control'] = false;
}

?>
<tr class="cart-item" <?= $styles['prod-tr'] ?> data-cart-item>
    <?php if ($config['has_remove_button']): ?>
        <td class="cart-item-remove-wrapper">
            <button class="cart-item-remove" type="button" onclick="Simpleshop.removeCartItem(this, '<?= $key ?>')">
                <i class="fal fa-trash-alt"></i>
            </button>
        </td>
    <?php endif; ?>
    <?php if ($config['has_image']): ?>
        <td class="cart-item-image-wrapper" <?= $styles['prod-td'] ?>>
            <a href="<?= $product_url ?>" class="cart-item-image">
                <?= Resource::getImgTag($picture, FragmentConfig::getValue('email_styles.use_mail_styles', false) ? 'email_product_thumb' : 'product_thumb') ?>
            </a>
        </td>
    <?php endif; ?>
    <td class="cart-item-name-wrapper" <?= $styles['prod-td'] ?>>
        <h3 class="cart-item-name" <?= $styles['h3'] ?>>
            <?= $product->getName() ?>
        </h3>
        <?php if ($category && !$is_giftcard): ?>
            <span class="cart-item-description" <?= $styles['p'] ?>>
                <?= $category->getName() ?>
            </span>
        <?php elseif ($extras['coupon_code']): ?>
            <br/>
            <code <?= $styles['code'] ?>>
                Code: <?= $extras['coupon_code'] ?>
            </code>
        <?php endif; ?>
        <?php if (count($features)): ?>
            <?php foreach ($features as $featureValue): ?>
                <?php
                $feature = Feature::get($featureValue->getValue('feature_id'));
                ?>
                <div class="feature">
                    <span class="label"><?= $feature->getName() ?>:</span>
                    <?= $featureValue->getName() ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </td>
    <td class="cart-item-price-wrapper" <?= $styles['prod-td'] ?>>
        <span class="label-mobile"><?= $useTaxPrices ? '###label.single_price###' : '###label.single_price_no_vat###' ?>:</span>
        <strong>&euro;&nbsp;<?= format_price($price) ?></strong>
        <?php if ($discount): ?>
            <div class="product-discount"><?= $discount->getName() ?></div>
        <?php endif; ?>
    </td>
    <td class="cart-item-amount-wrapper" <?= $styles['prod-td'] ?>>
        <?php
        $fragment = new \rex_fragment();
        $fragment->setVar('cart-quantity', $quantity);
        $fragment->setVar('product_key', $key);
        $fragment->setVar('config', $this->getVar('cart_button_config'));
        $fragment->setVar('max_amount', $product->getValue('amount'));
        echo $fragment->parse('simpleshop/cart/button.php');
        ?>
    </td>
    <td class="cart-item-total-wrapper" <?= $styles['prod-td'] ?>>
        <span class="label-mobile">###label.total###:</span>
        <strong>&euro;&nbsp;<?= format_price($price * $quantity) ?></strong>
    </td>
</tr>