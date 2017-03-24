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


$order      = $this->getVar('order');
$order_id   = $order->getValue('id');
$address_1  = $order->getValue('address_1');
$address_2  = $order->getValue('address_2');
$shipping   = $order->getValue('shipping');
$payment    = $order->getValue('payment');
$promotions = $order->getValue('promotions');
$extras     = $order->getValue('extras');

$products  = [];
$_products = OrderProduct::query()->where('order_id', $order_id)->find();


foreach ($_products as $product)
{
    $_product = $product->getValue('data');
    $_product->setValue('cart_quantity', $product->getValue('quantity'));
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
    $this->setVar('address', $address_1);
    $this->setVar('title', '###shop.invoice_address###');
    $this->setVar('has_edit_link', FALSE);
    $this->subfragment('simpleshop/checkout/summary/address_item.php');
    ?>

    <?php
    if ($extras['address_extras']['use_shipping_address'])
    {
        $this->setVar('address', $address_2);
    }
    else
    {
        $this->setVar('address', $address_1);
    }
    $this->setVar('title', '###shop.shipping_address###');
    $this->setVar('has_edit_link', FALSE);
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
        <th>###shop.single_price###</th>
        <th>###shop.amount###</th>
        <th>###label.total###</th>
    </tr>
    </thead>
    <tbody>

    <?php
    foreach ($products as $product)
    {
        $this->setVar('product', $product);
        $this->setVar('has_quantity_control', FALSE);
        $this->setVar('has_remove_button', FALSE);
        $this->setVar('has_image', FALSE);
        echo $this->subfragment('simpleshop/cart/item.php');
    }
    ?>

    </tbody>
</table>

<!-- Summe -->
<?php

$discounts = [];

if ($promotions)
{
    foreach ($promotions as $promotion)
    {
        if ($promotion->getValue('discount'))
        {
            $discounts[] = [
                'name'  => $promotion->getValue(sprogfield('name')),
                'value' => $promotion->getValue('discount'),
            ];
        }
    }
}
$this->setVar('discounts', $discounts);
$this->subfragment('simpleshop/checkout/summary/conclusion.php');
?>

