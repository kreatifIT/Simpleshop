<?php

/**
 * This file is part of the Simpleshop package.
 *
 * @author Kreatif GmbH
 * @author a.platter@kreatif.it
 * Date: 07.08.18
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace FriendsOfREDAXO\Simpleshop;


$User   = $this->getVar('User');
$method = $this->getVar('method');

if ($method == 'detail') {
    $content  = 'detail.php';
    $order_id = rex_get('order_id', 'int');
    $Order    = $order_id > 0 ? Order::get($order_id) : null;

    if (!$Order || $Order->getValue('customer_id') != $User->getId()) {
        $this->subfragment('simpleshop/customer/auth/no_permission.php');
        return;
    }
    $this->setVar('Order', $Order);
} else {
    $content    = 'history_list.php';
    $collection = Order::query()
        ->where('customer_id', $User->getId())
        ->find();

    $this->setVar('orders', $collection);
}

?>
<div class="customer-area-orders">
    <?php $this->subfragment('simpleshop/customer/customer_area/title.php') ?>
    <?php $this->subfragment('simpleshop/customer/customer_area/order/' . $content) ?>
</div>