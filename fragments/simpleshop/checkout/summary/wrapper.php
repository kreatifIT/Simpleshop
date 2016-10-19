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

$Order     = $_SESSION['checkout']['Order'];
$errors    = $Order->calculateDocument();
$address_1 = $Order->getValue('address_1');
$address_2 = $Order->getValue('address_2');
$shipping  = $Order->getValue('shipping');
$payment   = $Order->getValue('payment');

if (count($errors)): ?>
    <div class="row column">
    <?php foreach ($errors as $error): ?>
        <div class="callout alert margin-bottom">
            <p><?= isset($error['replace']) ? strtr($error['label'], ['{{replace}}' => $error['replace']]) : $error['label'] ?></p>
        </div>
    <?php endforeach; ?>
        </div>
<?php endif; ?>
<!-- Adressen -->
<div class="row address-panels">
    <h3>###label.summary_order###</h3>

    <?php
    $this->setVar('title', '###shop.invoice_address###');
    $this->setVar('url', rex_getUrl(NULL, NULL, ['step' => 2]));
    $this->setVar('address', $address_1);
    $this->subfragment('simpleshop/checkout/summary/address_item.php');
    ?>

    <?php
    $this->setVar('title', '###shop.shipping_address###');
    $this->setVar('url', rex_getUrl(NULL, NULL, ['step' => 2]));
    $this->setVar('address', $address_2);
    $this->subfragment('simpleshop/checkout/summary/address_item.php');
    ?>

</div>

<!-- Lieferung & Zahlung -->
<div class="row radio-panels">

    <div class="medium-6 columns margin-bottom">
        <h3>###label.shipment###</h3>

        <?php
        $before = '
            <a href="' . rex_getUrl(NULL, NULL, ['step' => 3]) . '" class="edit">
                <i class="fa fa-pencil hide-for-large" aria-hidden="true"></i>
                <span class="show-for-large">###action.edit###</span>
            </a>
        ';
        $this->setVar('before', $before, FALSE);
        $this->setVar('shipping', $shipping);
        $this->setVar('name', $shipping->getName());
        $this->setVar('plugin_name', $shipping->getPluginName());
        $this->subfragment('simpleshop/checkout/shipping_and_payment/shipping_item.php');
        ?>
    </div>

    <div class="medium-6 columns margin-bottom">
        <h3>###label.payment###</h3>

        <?php
        $before = '
            <a href="' . rex_getUrl(NULL, NULL, ['step' => 3]) . '" class="edit">
                <i class="fa fa-pencil hide-for-large" aria-hidden="true"></i>
                <span class="show-for-large">###action.edit###</span>
            </a>
        ';
        $this->setVar('before', $before, FALSE);
        $this->setVar('payment', $payment);
        $this->setVar('name', $payment->getName());
        $this->setVar('plugin_name', $payment->getPluginName());
        $this->subfragment('simpleshop/checkout/shipping_and_payment/payment_item.php');
        ?>

    </div>
</div>
<!-- Warenkorb -->
<div class="shop row column">

    <?php
    $this->setVar('has_quantity_control', FALSE);
    $this->setVar('has_quantity', TRUE);
    $this->setVar('has_remove_button', FALSE);
    $this->subfragment('simpleshop/product/general/cart/wrapper.php');
    ?>

</div>

<!-- Summe -->
<div class="row column">
    <div class="order-total">
        <div class="subtotal">
            <span>&euro; <?= format_price($Order->getValue('tax')) ?></span>
            <span>###label.tax_included###</span>
        </div>
        <div class="subtotal">
            <span>&euro; <?= format_price($Order->getValue('subtotal')) ?></span>
            <span>###label.subtotal###</span>
        </div>
        <div class="subtotal ">
            <span>&euro; <?= format_price($Order->getValue('shipping_costs')) ?></span>
            <span>###label.shipment_cost###</span>
        </div>
        <div class="subtotal total">
            <span>&euro; <?= format_price($Order->getValue('total')) ?></span>
            <span>###label.total_sum###</span>
        </div>
    </div>
</div>

<form action="" method="post">
    <!-- AGB -->
    <div class="terms-of-service row column">
        <div class="custom-checkbox margin-small-bottom">
            <label>
                * ###label.tos###
                <input name="tos_accepted" value="1" type="checkbox"/>
                <span class="checkbox"></span>
            </label>
        </div>
        <div class="custom-checkbox">
            <label>
                * ###label.cancellation_terms###
                <input name="rma_accepted" value="1" type="checkbox"/>
                <span class="checkbox"></span>
            </label>
        </div>
    </div>

    <div class="row buttons-checkout margin-bottom">
        <div class="medium-6 medium-offset-6 columns">
            <button type="submit" name="action" value="place_order" class="button button-checkout">
                ###action.place_order###
            </button>
        </div>
    </div>
</form>
