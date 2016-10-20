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

$shippings = Shipping::getAll();
$payments  = Payment::getAll();

if ($_SESSION['checkout']['Order'])
{
    $this->setVar('shipping', $_SESSION['checkout']['Order']->getValue('shipping'));
    $this->setVar('payment', $_SESSION['checkout']['Order']->getValue('payment'));
}

?>
<form action="" method="post">
    <div class="radio-panels row column margin-top">
        <!-- Shipment -->
        <h3>###label.shipment###</h3>

        <fieldset class="row">
            <?php
            $this->setVar('class', 'medium-6 columns margin-bottom');
            
            foreach ($shippings as $shipping)
            {
                $this->setVar('name', $shipping->getName());
                $this->setVar('plugin_name', $shipping->getPluginName());
                echo $this->subfragment('simpleshop/checkout/shipping_and_payment/shipping_item.php');
            }
            ?>
        </fieldset>

        <span class="horizontal-rule large-rule margin-bottom"></span>

        <!-- Payment -->
        <h3>###label.payment###</h3>

        <fieldset class="row">

            <?php
            foreach ($payments as $payment)
            {
                $this->setVar('name', $payment->getName());
                $this->setVar('plugin_name', $payment->getPluginName());
                echo $this->subfragment('simpleshop/checkout/shipping_and_payment/payment_item.php');
            }
            ?>

        </fieldset>
    </div>
    <div class="row buttons-checkout margin-bottom">
        <div class="medium-6 columns">
            <a href="<?= rex_getUrl(null, null, ['step' => 2]) ?>" class="button button-gray">###action.go_back###</a>
        </div>
        <div class="medium-6 columns">
            <button type="submit" name="action" value="process_shipping_and_payment" class="button button-checkout">###action.go_ahead###</button>
        </div>
    </div>
</form>
