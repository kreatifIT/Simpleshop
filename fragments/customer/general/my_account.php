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

//Customer::logout();
//Customer::login('a.platter@kreatif.it', '654654');
Session::clearCart();
Session::addProduct(Session::getProductKey(2));
Session::addProduct(Session::getProductKey(1,[1,4]), 7);
Session::addProduct(Session::getProductKey(1,[3,4]), 10);
//Session::addProduct(Session::getProductKey(1,[1,5]));
//$cart_items = Session::getCartItems();
//pr($cart_items[0]);

pr(Session::getCartItems(TRUE));

?>
<div class="<?= $this->getVar('class') ?>">
    <div class="large-3 medium-5 columns">
        <?php
//        $this->setVar('class', 'large-6 columns margin-bottom');
//        echo $this->subfragment('customer/auth/login.php');
        ?>
        <div class="shop-accordion-menu margin-bottom">
            <ul data-accordion-menu>
                <li>
                    <a href="#">###label.welcome###</a>
                    <ul class=is-active">
                        <li>
                            <a href="#">Subkategorie 1A</a>
                            <ul class="">
                                <li><a href="#">Item 1Ai</a></li>
                                <li><a href="#">Item 1Aii</a></li>
                                <li><a href="#">Item 1Aiii</a></li>
                            </ul>
                        </li>
                        <li><a href="#">Subkategorie 1B</a></li>
                        <li><a href="#">Subkategorie 1C</a></li>
                    </ul>
                </li>
                <li>
                    <a href="#">###label.account###</a>
                    <ul class="">
                        <li><a href="#">Subkategorie 2A</a></li>
                        <li><a href="#">Subkategorie 2B</a></li>
                    </ul>
                </li>
                <li><a href="#">###shop.invoice_shipment_address###</a></li>
                <li><a href="#">###label.orders###</a></li>
                <li><a href="<?= rex_getUrl(null, null, ['action' => 'logout']) ?>">###action.logout###</a></li>
            </ul>
        </div>
    </div>
    <div class="large-9 medium-7 columns">
        <p>Guten Tag Herr <strong>Untertrifaller!</strong> <br> Ihr letzter Besuch war am Dienstag, den 17. Oktober
            2015.</p>
        <span class="double-rule"></span>
        <p>
            Willkommen in Ihrem persönlichen Bereich von Rasenfix!
            Hier können Sie Ihre <a href="#">persönlichen Daten</a>, Ihre Rechnungs- und Lieferadresse verwalten
            sowie Ihre Bestellungen
            einsehen.
        </p>
    </div>
</div>
