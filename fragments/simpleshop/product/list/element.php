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

$product     = $this->getVar('product');
$image       = $product->getValue('image');
$badge       = $product->getValue('badge');
$price       = $product->getPrice(TRUE, FALSE);
$offer_price = $product->getValue('reduced_price');
$variants    = $product->getFeatureVariants();
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
            <div class="ribbon"><span>###label.offer###</span></div>
        <?php endif; ?>

        <a href="<?= $product_url ?>"><?= Utils::getImageTag($image, 'product-list-element-main') ?></a>
        <h3><a href="<?= $product_url ?>"><?= $product->getValue(sprogfield('name')) ?></a></h3>
        <div class="product-price">
            <span class="price-was <?php if ($offer_price <= 0) echo 'hidden' ?>">&euro; <?= format_price($price) ?></span>
            <span class="price">&euro; <?= format_price($offer_price > 0 ? $product->getPrice(true) : $price) ?></span>
        </div>
        <?php
        if(count($variants['mapping']))
        {
            $variant_key = array_keys($variants['variants']);
        }
        else
        {
            $variant_key = [''];
        }
        $this->setVar('is_disabled', $product->getValue('amount') <= 0);
        $this->setVar('product_key', $product->getValue('id').'|'. $variant_key[0]);
        $this->setVar('has_quantity_control', FALSE);
        $this->setVar('has_quantity', FALSE);
        $this->setVar('has_add_to_cart_button', TRUE);
        echo $this->subfragment('simpleshop/product/general/cart/button.php');
        ?>
    </div>
</div>