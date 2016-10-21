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

//        Customer::logout();
//        Customer::login('a.platter@kreatif.it', '654654');
//        Session::clearCart();
//        Session::addProduct(Session::getProductKey(2));
        Session::addProduct(Session::getProductKey(1, [1, 4]), 7);
        Session::addProduct(Session::getProductKey(1, [3, 4]), 10);
        Session::addProduct(Session::getProductKey(1, [1, 5]));

        pr(Session::getCartItems(TRUE));

?>
<p>Guten Tag Herr <strong>Untertrifaller!</strong> <br> Ihr letzter Besuch war am Dienstag, den 17.
    Oktober
    2015.</p>
<span class="double-rule"></span>
<p>
    Willkommen in Ihrem persönlichen Bereich von Rasenfix!
    Hier können Sie Ihre <a href="#">persönlichen Daten</a>, Ihre Rechnungs- und Lieferadresse verwalten
    sowie Ihre Bestellungen
    einsehen.
</p>
