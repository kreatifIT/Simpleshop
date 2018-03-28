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

$products = $this->getVar('products', []);

?>

<?php foreach ($products as $product) : ?>

    <?php

    $images      = $product->getArrayValue('images');
    $picture     = array_shift($images);
    $price       = $product->getPrice();
    $quantity    = $product->getValue('cart_quantity');
    $product_url = $product->getUrl();
    $key         = $product->getValue('key');

    ?>

    <div class="offcanvas-cart-item" data-cart-item="">
        <a class="image" href="<?= $product_url ?>">
            <?= Resource::getImgTag($picture, 'product_gallery_thumb') ?>
        </a>
        <div class="description">
            <?php if ($quantity) : ?>
                <span class="quantity"><?= $quantity ?> x </span>
            <?php endif; ?>
            <span class="name"><?= $product->getName() ?></span>
            <span class="price">&euro;&nbsp;<?= format_price($price) ?></span>
        </div>
        <div class="remove" onclick="Simpleshop.removeCartItem(this, <?= $key ?>, 'offcanvas_cart')"></div>
    </div>
<?php endforeach; ?>