<?php

/**
 * This file is part of the Kreatif\Project package.
 *
 * @author Kreatif GmbH
 * @author a.platter@kreatif.it
 * Date: 11.06.18
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FriendsOfREDAXO\Simpleshop;

$products = $this->getVar('products', []);
$product_added = $this->getVar('product_added', false);
$Settings = \rex::getConfig('simpleshop.Settings');


if (count($products) == 0):
    $fragment = new \rex_fragment();
    echo $fragment->parse('simpleshop/cart/empty.php');
    return;
else: ?>
    <?php if ($product_added): ?>
        <div class="offcanvas-cart-success">
            <span class="check"><i class="far fa-check"></i></span>
            <span class="description">###simpleshop.add_to_cart_success###</span>
        </div>
    <?php endif; ?>

    <div class="offcanvas-cart-content">
        <div class="offcanvas-cart-items" data-cart-item-container>
            <?= $this->subfragment('simpleshop/cart/offcanvas/items.php') ?>
        </div>

        <div class="offcanvas-cart-prices">
            <div class="price">
                <span class="label">###label.total###</span>
                <div class="amount">
                    &euro;&nbsp;<span data-cart-item-total=""><?= format_price(Session::getTotal()) ?></span>
                </div>
            </div>
        </div>

        <div class="offcanvas-cart-buttons">
            <a href="<?= rex_getUrl($Settings['linklist']['cart']) ?>" class="button expanded margin-small-bottom">###action.proceed_to_checkout###</a>
        </div>
    </div>
<?php endif; ?>