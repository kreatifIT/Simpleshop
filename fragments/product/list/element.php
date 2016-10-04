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
$price       = $product->getPrice();
$product_url = '';

if (strlen($image) == 0 && strlen($product->getValue('gallery')))
{
    $gallery = explode(',', $product->getValue('gallery'));
    $image   = $gallery[0];
}

$rand_num = rand(1, 5); ?>
<div class="column">
    <div class="shop-product-item" itemscope="" itemtype="http://schema.org/Product">
        <?php if (strlen($badge)) : ?>
            <div class="badge">
                <?= Utils::getImageTag($badge, '') ?>
            </div>
        <?php endif; ?>
        <?php if ($rand_num == 2) : // TODO: angebot einbauen ?>
            <div class="ribbon"><span>###label.offer###</span></div>
        <?php endif; ?>

        <a href="<?= $product_url ?>"><?= Utils::getImageTag($image, 'product-list-element-main') ?></a>
        <h3><a href="<?= $product_url ?>"><?= $product->getValue('name_' . $lang_id) ?></a></h3>
        <div class="product-price">
            <?php // TODO: Angebotspreis eintragen
            ?>
            <span class="price-was">&euro; <?= $price ?></span>
            <span class="price">&euro; <?= $price ?></span>
        </div>

        <a class="add-to-cart fbox" href="#proceed-to-cart-<?= $i ?>">
            <i class="fa fa-cart-plus" aria-hidden="true"></i>
            <span>###action.add_to_cart###</span>
        </a>
    </div>

    <!-- Popup -->
    <?php
        $this->subfragment('general/cart_popup.php');
    ?>
</div>