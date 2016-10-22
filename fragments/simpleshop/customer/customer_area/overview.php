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

use Sprog\Wildcard;

$User = $this->getVar('user');
$name = trim($User->getValue('firstname') ." ". $User->getValue('lastname'));

//        Customer::logout();
//        Customer::login('a.platter@kreatif.it', '654654');
//        Session::clearCart();
//        Session::addProduct(Session::getProductKey(2));
//        Session::addProduct(Session::getProductKey(1, [1, 4]), 7);
//        Session::addProduct(Session::getProductKey(1, [3, 4]), 10);
//        Session::addProduct(Session::getProductKey(1, [1, 5]));
//
//        pr(Session::getCartItems(TRUE));

?>
<p><?= strtr(Wildcard::get('shop.my_account_overview_text'), ['{{name}}' => '<strong>'. $name .'</strong>']) ?>.</p>
