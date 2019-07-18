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


$Order = $this->getVar('Order');
$order_id = $Order->getValue('id');
$Config = FragmentConfig::getValue('checkout');

$products  = [];
$_products = OrderProduct::query()->where('order_id', $order_id)->find();


foreach ($_products as $product) {
    $_product = $product->getValue('data');
    $_product->setValue('cart_quantity', $product->getValue('cart_quantity'));
    $_product->setValue('code', $product->getValue('code'));
    $products[] = $_product;
}


?>
    <h1 class="heading">###simpleshop.order_num### <?= $order_id ?></h1>
    <?php

$Config['has_coupons']        = false;
$Config['has_summary_footer'] = false;

$fragment = new \rex_fragment();
$fragment->setVar('Order', $Order);
$fragment->setVar('Config', $Config);
$fragment->setVar('products', $Order->getProducts(false));
echo $fragment->parse('simpleshop/checkout/summary/wrapper.php');
