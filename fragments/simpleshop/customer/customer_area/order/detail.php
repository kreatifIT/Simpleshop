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


$order    = $this->getVar('order');
$order_id = $order->getValue('id');
$SAdress  = $order->getShippingAddress();
$IAdress  = $order->getInvoiceAddress();
$shipping = $order->getValue('shipping');
$payment  = $order->getValue('payment');

$products  = [];
$_products = OrderProduct::query()
    ->where('order_id', $order_id)
    ->find();


foreach ($_products as $product) {
    $_product = $product->getValue('data');
    $_product->setValue('cart_quantity');
    $_product->setValue('code', $product->getValue('code'));
    $products[] = $_product;
}

?>

<div class="row">
    <div class="column order-title margin-bottom">
        <h1>Order #<?= $order_id ?></h1>
    </div>
</div>

<div class="row">

    <?php
    $this->setVar('address', $IAdress);
    $this->setVar('title', '###label.invoice_address###');
    $this->setVar('has_edit_link', false);
    $this->subfragment('simpleshop/checkout/summary/address_item.php');
    ?>

    <?php
    $this->setVar('address', $SAdress);
    $this->setVar('title', '###label.shipping_address###');
    $this->setVar('has_edit_link', false);
    $this->subfragment('simpleshop/checkout/summary/address_item.php');
    ?>
</div>

<div class="row">
    <?php if ($shipping): ?>
        <div class="medium-6 columns margin-bottom">
            <h3>###label.shipment###</h3>
            <p><?= $shipping->getName() ?></p>
        </div>
    <?php endif; ?>

    <div class="medium-6 columns margin-bottom">
        <h3>###label.payment###</h3>
        <p><?= $payment->getName() ?><br/><?= $payment->getValue('info') ?></p>
    </div>
</div>

<table class="table stack">
    <thead>
    <tr>
        <th>###shop.produt_code###</th>
        <th>###simpleshop.single_price_no_vat###</th>
        <th>###shop.amount###</th>
        <th>###label.total###</th>
    </tr>
    </thead>
    <tbody>

    <?php
    foreach ($products as $product) {
        $this->setVar('product', $product);
        $this->setVar('has_quantity_control', false);
        $this->setVar('has_remove_button', false);
        $this->setVar('has_image', false);
        echo $this->subfragment('simpleshop/cart/item.php');
    }
    ?>

    </tbody>
</table>

<!-- Summe -->
<?php
$this->subfragment('simpleshop/checkout/summary/conclusion.php');
?>

