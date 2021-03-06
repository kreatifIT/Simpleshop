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

$Controller = CartController::execute([
    'check_cart'      => rex_server('HTTP_X_PJAX', 'string', false),
    'apply_coupon'    => false,
    'apply_discounts' => false,
    'apply_shipping'  => false,
]);

$tpl = count($Controller->getProducts()) ? 'simpleshop/cart/offcanvas/container.php' : null;

?>
<div class="offcanvas-cart">
    <div class="offcanvas-cart-inner">

        <button class="offcanvas-cart-continue-shopping" type="button" onclick="Simpleshop.closeOffcanvasCart();">
            ###label.cart###
            <span class="icon-close"></span>
        </button>

        <div data-cart-container>
            <?= $Controller->parse($tpl); ?>
        </div>
    </div>
</div>
