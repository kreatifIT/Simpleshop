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
$image   = $product->getValue('image');

if (strlen($image) == 0 && strlen($product->getValue('gallery')))
{
    $gallery = explode(',', $product->getValue('gallery'));
    $image   = $gallery[0];
}

?>
<div class="shop-modal">
    <div class="row">
        <div class="small-4 columns">
            <div class="image">
                <?= Utils::getImageTag($image, 'cart-list-element-main') ?>
            </div>
        </div>
        <div class="small-8 columns">
            <div class="description">
                Lorem ipsum dolor sit amet, <a></a>. Accusamus animi
                ipsam nam.
            </div>
        </div>
    </div>
    <span class="row column horizontal-rule double-rule"></span>
    <div class="row buttons-checkout">
        <div class="medium-6 columns">
            <a href="#" class="button button-gray">###action.continue_shopping###</a>
        </div>
        <div class="medium-6 columns">
            <a href="<?= rex_getUrl(62) ?>" class="button button-checkout">###action.go_to_cart###</a>
        </div>
    </div>
</div>