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
use Kreatif\Resource;

$picture = null;
$ctrlTpl = '';

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
            <span class="amount"><?= format_price(Session::getTotal()) ?></span>
        </div>
    </div>
    <div class="offcanvas-cart-buttons">
        <a href="<?= rex_getUrl(Settings::NEWS_PAGE_ID) ?>" class="button secondary expanded margin-small-bottom">###action.proceed_to_checkout###</a>
        <a href="<?= rex_getUrl(Settings::CART_PAGE_ID) ?>" class="button expanded">###action.action.shop_edit_cart###</a>
    </div>
</div>