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


?>
<!-- Adressen -->
<div class="row address-panels">
    <h3>###label.summary_order###</h3>
    <div class="medium-6 columns margin-bottom">
        <div class="address-panel">
            <a href="#" class="edit">
                <i class="fa fa-pencil hide-for-large" aria-hidden="true"></i>
                <span class="show-for-large">###action.edit###</span>
            </a>
            <h4>###shop.invoice_address###</h4>
            <p>
                Max Mustermann <br>
                Mustermann Straße  37 <br>
                12345 Bozen - Italien <br>
                <br>
                max@mustermann.com <br>
                +39 123 456 789 <br>
            </p>
        </div>
    </div>
    <div class="medium-6 columns margin-bottom">
        <div class="address-panel">
            <a href="#" class="edit">
                <i class="fa fa-pencil hide-for-large" aria-hidden="true"></i>
                <span class="show-for-large">###action.edit###</span>
            </a>
            <h4>###shop.shipping_address###</h4>
            <p>
                Max Mustermann <br>
                Mustermann Straße  37 <br>
                12345 Bozen - Italien <br>
                <br>
                max@mustermann.com <br>
                +39 123 456 789 <br>
            </p>
        </div>
    </div>
</div>
<!-- Lieferung & Zahlung -->
<div class="row radio-panels">
    <div class="medium-6 columns margin-bottom">
        <h3>###label.shipment###</h3>
        <div class="radio-panel selected">
            <a href="#" class="edit">
                <i class="fa fa-pencil hide-for-large" aria-hidden="true"></i>
                <span class="show-for-large">###action.edit###</span>
            </a>
            <i class="fa fa-truck" aria-hidden="true"></i>
            <div class="custom-radio">
                <label for="standard">
                    ###label.shipping_standard###
                    <input type="radio" name="shipment" value="Standard" id="standard" checked required>
                    <span class="radio"></span>
                </label>
            </div>
        </div>
    </div>
    <div class="medium-6 columns margin-bottom">
        <h3>###label.payment###</h3>
        <div class="radio-panel selected">
            <a href="#" class="edit">
                <i class="fa fa-pencil hide-for-large" aria-hidden="true"></i>
                <span class="show-for-large">###action.edit###</span>
            </a>
            <i class="fa fa-cc-paypal" aria-hidden="true"></i>
            <div class="custom-radio">
                <label for="paypal">
                    ###label.payment_paypal###
                    <input type="radio" name="payment" value="Paypal" id="paypal" checked required>
                    <span class="radio"></span>
                </label>
            </div>
        </div>
    </div>
</div>
<!-- Warenkorb -->
<div class="shop row column">
    <table class="cart-content stack">
        <thead>
        <tr class="cart-header">
            <th>###label.preview###</th>
            <th>###label.product###</th>
            <th>###label.price###</th>
            <th>###label.amount###</th>
            <th>###label.total###</th>
            <th>&nbsp;</th>
        </tr>
        </thead>
        <tbody>
        <?php
        for ($i = 0; $i < 5; $i++) {
            echo 'REX_TEMPLATE[40]';
        }
        ?>
        </tbody>
    </table>
</div>
<!-- Summe -->
<div class="row column">
    <div class="order-total">
        <div class="subtotal">
            <span>€ 768,00</span>
            <span>###label.subtotal###</span>
        </div>
        <div class="subtotal ">
            <span>€ 25,20</span>
            <span>###label.shipment_cost###</span>
        </div>
        <div class="subtotal">
            <span>€ 184,34</span>
            <span>###label.vat_short###</span>
        </div>
        <div class="subtotal total">
            <span>€ 968,00</span>
            <span>###label.total_sum###</span>
        </div>
    </div>
</div>
<!-- AGB -->
<div class="terms-of-service row column">
    <div class="custom-checkbox margin-small-bottom">
        <label for="checkbox1">
            * ###label.tos###
            <input id="checkbox1" type="checkbox">
            <span class="checkbox"></span>
        </label>
    </div>
    <div class="custom-checkbox">
        <label for="checkbox2">
            * ###label.cancellation_terms###
            <input id="checkbox2" type="checkbox">
            <span class="checkbox"></span>
        </label>
    </div>
</div>


<div class="row buttons-checkout margin-bottom">
    <div class="medium-6 medium-offset-6 columns">
        <a href="#" class="button button-checkout">###action.place_order###</a>
    </div>
</div>

