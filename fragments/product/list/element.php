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

$lang_id     = \rex_clang::getCurrentId();
$product     = $this->getVar('product');
$image       = $product->getValue('image');
$badge       = $product->getValue('badge');
$price       = $product->getValue('price');
$offer_price = $product->getValue('reduced_price');
$product_url = $product->getUrl();

if (strlen($image) == 0 && strlen($product->getValue('gallery')))
{
    $gallery = explode(',', $product->getValue('gallery'));
    $image   = $gallery[0];
}

?>
<div class="column">
    <div class="shop-product-item" itemscope="" itemtype="http://schema.org/Product">
        <?php if (strlen($badge)) : ?>
            <div class="badge">
                <?= Utils::getImageTag($badge, '') ?>
            </div>
        <?php endif; ?>
        <?php if ($offer_price > 0): ?>
            <div class="ribbon"><span><?= $this->i18n('label.offer'); ?></span></div>
        <?php endif; ?>

        <a href="<?= $product_url ?>"><?= Utils::getImageTag($image, 'product-list-element-main') ?></a>
        <h3><a href="<?= $product_url ?>"><?= $product->getValue('name_' . $lang_id) ?></a></h3>
        <div class="product-price">
            <?php if ($offer_price > 0): ?>
                <span class="price-was">&euro; <?= format_price($price) ?></span>
            <?php endif; ?>
            <span class="price">&euro; <?= format_price($offer_price > 0 ? $offer_price : $price) ?></span>
        </div>

        <?php
        $this->setVar('button-cart-counter', $i);
        $this->setVar('has_quantity_control', FALSE);
        $this->setVar('has_add_to_cart_button', TRUE);
        echo $this->subfragment('product/general/cart/button.php');
        ?>
    </div>

    <!-- Popup -->
    <?php
    $this->subfragment('product/general/cart/popup.php');
    ?>
</div>