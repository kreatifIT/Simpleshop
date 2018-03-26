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
<div class="cart-aside">
    <button type="button">###label.continue_shopping###</button>
    <div class="cart">
        <div class="cart-item">
            <a class="cart-item-image" href="#">
                <?= Resource::getImgTag($picture, 'cart-list-element-main') ?>
            </a>
            <div class="cart-item-description">
                <span class="amount">2x </span>
                <span class="name">Lorem ipsum dolor sit amet.</span>
                <span class="price">1400.00 €</span>
            </div>
            <div class="cart-item-remove">x</div>
        </div>
    </div>
    <div class="cart-prices">
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
    <div class="cart-buttons">
        <a href="" class="button secondary">###action.proceed_to_checkout###</a>
        <a href="" class="button secondary">###action.action.shop_edit_cart###</a>
    </div>
</div>