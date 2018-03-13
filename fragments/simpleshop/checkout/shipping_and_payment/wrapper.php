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

$shippings = $this->getVar('shippings');
$payments  = $this->getVar('payments');
$Order     = $this->getVar('Order');

$shipping = $Order->getValue('shipping');
$payment  = $Order->getValue('payment');

$this->setVar('shipping', $shipping ?: $shippings[0]);
$this->setVar('payment', $payment ?: $payments[0]);

?>
<div class="shipping-payment">
    <form action="" method="post">

        <div class="shippings margin-top margin-large-bottom">
            <!-- Shipment -->
            <div class="row column margin-small-bottom">
                <h2>###label.shipping_method###</h2>
            </div>

            <?php
            foreach ($shippings as $shipping) {
                $this->setVar('name', $shipping->getName());
                $this->setVar('plugin_name', $shipping->getPluginName());
                echo $this->subfragment('simpleshop/checkout/shipping_and_payment/shipping_item.php');
            }
            ?>
        </div>

        <div class="payments margin-top margin-large-bottom">
            <!-- Payment -->
            <div class="row column margin-small-bottom">
                <h2>###label.payment_method###</h2>
            </div>


            <?php
            foreach ($payments as $payment) {
                $this->setVar('name', $payment->getName());
                $this->setVar('plugin_name', $payment->getPluginName());
                echo $this->subfragment('simpleshop/checkout/shipping_and_payment/payment_item.php');
            }
            ?>
        </div>

        <div class="row column margin-large-bottom">
            <a href="#" class="button margin-bottom">###action.go_back###</a>
            <button type="submit" class="button margin-bottom secondary float-right" name="action" value="set-shipping-payment">###action.go_ahead###</button>
        </div>

    </form>
</div>
