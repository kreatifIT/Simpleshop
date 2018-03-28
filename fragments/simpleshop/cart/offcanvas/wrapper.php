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

use Kreatif\Project\Settings;

$ctrlTpl  = '';
$picture  = null;
$Settings = \rex::getConfig('simpleshop.Settings');

$Controller = \FriendsOfREDAXO\Simpleshop\CartController::execute();

if (count($Controller->getProducts())) {
    $ctrlTpl = 'simpleshop/cart/offcanvas/items.php';
}



?>
<div class="offcanvas-cart">
    <div class="offcanvas-cart-inner">
        <button class="offcanvas-cart-continue-shopping" type="button">
            <?= file_get_contents(\rex_path::addonAssets('simpleshop', 'img/back.svg')); ?>
            ###label.continue_shopping###
        </button>
        <div class="offcanvas-cart-success">
            <span class="check">✔</span>
            <span class="description">###simpleshop.add_to_cart_success###</span>
        </div>

        <div class="offcanvas-cart-items" data-cart-item-container="">
            <?= $Controller->parse($ctrlTpl) ?>
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
            <a href="<?= rex_getUrl($Settings['linklist']['cart']) ?>" class="button secondary expanded margin-small-bottom">###action.proceed_to_checkout###</a>
        </div>
    </div>
</div>