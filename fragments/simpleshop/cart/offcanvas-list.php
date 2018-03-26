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
<div class="offcanvas-cart-items">
    <?php foreach ($products as $product) : ?>

        <?php

        $Customer   = Customer::getCurrentUser();
        $isCompany = $Customer ? $Customer->isCompany() : false;

        $images      = $product->getArrayValue('images');
        $picture     = array_shift($images);
        $price       = $product->getPrice(!$isCompany);
        $quantity    = $product->getValue('cart_quantity');
        $product_url = $product->getUrl();

        ?>


        <div class="offcanvas-cart-item">
            <a class="image" href="<?= $product_url ?>">
                <?= Resource::getImgTag($picture, 'cart-list-element-main') ?>
            </a>
            <div class="description">
                <?php if ($quantity) : ?>
                    <span class="quantity"><?= $quantity ?> x </span>
                <?php endif; ?>
                <span class="name"><?= $product->getName() ?></span>
                <span class="price"><?= $price ?></span>
            </div>
            <div class="remove"></div>
        </div>
    <?php endforeach; ?>
</div>