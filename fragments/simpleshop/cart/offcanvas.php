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

$picture = '';

?>
<div class="offcanvas-cart">
    <button class="offcanvas-cart-continue-shopping" type="button">###label.continue_shopping###</button>
    <div class="offcanvas-cart-success">
        <span class="check">✔</span>
        <span class="description">Der Artikel wurde erfolgreich in den Warenkorb gelegt</span>
    </div>

    <div class="offcanvas-cart-items">
        <div class="offcanvas-cart-item">
            <a class="image" href="#">
                <?= Resource::getImgTag($picture, 'cart-list-element-main') ?>
            </a>
            <div class="description">
                <span class="amount">2x </span>
                <span class="name">Lorem ipsum dolor sit amet.</span>
                <span class="price">1400.00 €</span>
            </div>
            <div class="remove"></div>
        </div>
    </div>
    <div class="offcanvas-cart-prices">
        <div class="price">
            <span class="label">Zwischensumme</span>
            <span class="amount">1.631,08 €</span>
        </div>
        <div class="price">
            <span class="label">Shipping costs</span>
            <span class="amount">143,99 €</span>
        </div>
        <div class="price">
            <span class="label">Total</span>
            <span class="amount">1.775,07 €</span>
        </div>
    </div>
    <div class="offcanvas-cart-buttons">
        <a href="" class="button secondary expanded margin-small-bottom">###action.proceed_to_checkout###</a>
        <a href="" class="button expanded">###action.action.shop_edit_cart###</a>
    </div>
</div>