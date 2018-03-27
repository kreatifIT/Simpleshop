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
    $ctrlTpl = 'simpleshop/cart/offcanvas-list.php';
}

?>
<div class="offcanvas-cart">
    <button class="offcanvas-cart-continue-shopping" type="button">###label.continue_shopping###</button>
    <div class="offcanvas-cart-success">
        <span class="check">✔</span>
        <span class="description">###simpleshop.add_to_cart_success###</span>
    </div>

    <?= $Controller->parse($ctrlTpl) ?>

    <div class="offcanvas-cart-prices">
        <div class="price">
            <span class="label">###label.total###</span>
            <span class="amount">&euro;&nbsp;<?= format_price(Session::getTotal()) ?></span>
        </div>
    </div>
    <div class="offcanvas-cart-buttons">
        <a href="<?= rex_getUrl($Settings['linklist']['cart']) ?>" class="button secondary expanded margin-small-bottom">###action.proceed_to_checkout###</a>
    </div>
</div>