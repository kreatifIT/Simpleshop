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
$action = rex_get('action', 'string');

$stmt = Order::query()
    ->where('customer_id', $User->getId());

$collection = $stmt->find();

$this->setVar('orders', $collection);

?>
<div class="member-area--history">
    <?php $this->subfragment('simpleshop/customer/customer_area/title.php') ?>
    <?php $this->subfragment('simpleshop/customer/customer_area/order/history_list.php') ?>
</div>