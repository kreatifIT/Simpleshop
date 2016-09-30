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

echo \rex_view::title('Simpleshop');

//Customer::login('a.platter@kreatif.it', '654654');
//Session::clearCart();
//Session::addProduct(Session::getProductKey(1,3));
//Session::addProduct(Session::getProductKey(1,[1,5]));
$cart_items = Session::getCartItems();
//pr($cart_items[0]);

//$Product = Product::get(1);
//pr($Product->getPrice());