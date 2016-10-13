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

use Kreatif\Settings;

$product = $this->getVar('product');
// Todo: remove these two lines
if (!$product)
    return;

$image = $product->getValue('image');


if (strlen($image) == 0 && strlen($product->getValue('gallery'))) {
    $gallery = explode(',', $product->getValue('gallery'));
    $image = $gallery[0];
}

?>
<div class="small-4 columns">
    <div class="image">
        <?= Utils::getImageTag($image, 'cart-list-element-main') ?>
    </div>
</div>
<div class="small-8 columns">
    <div class="description">Product ID <?= $product->getValue('name_1'); ?> added to Cart</div>
</div>