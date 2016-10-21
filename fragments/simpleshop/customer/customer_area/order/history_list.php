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

use Sprog\Wildcard;

$User      = Customer::getCurrentUser();
$user_id   = $User->getValue('id');
$order_id  = rex_get('order_id', 'int');
$show_list = TRUE;

if ($order_id)
{
    $Order = Order::get($order_id);
    // prove the user is associated to the order
    if ($Order && $Order->getValue('customer_id') == $user_id)
    {
        $show_list = FALSE;
        $this->setVar('order', $Order);
        $this->subfragment('simpleshop/customer/customer_area/order/detail.php');
    }
}

// ORDER LIST
if ($show_list):

    $orders = Order::query()
        ->where('customer_id', $user_id)
        ->find();

    ?>

    <?php if (count($orders)): ?>
    <table class="table scroll">
        <thead>
        <tr>
            <th>###shop.order_num###</th>
            <th>###label.date###</th>
            <th>###label.price###</th>
            <th>###label.status###</th>
            <th></th>
        </tr>
        </thead>
        <tbody>

        <?php foreach ($orders as $order): ?>
            <tr>
                <td><?= $order->getValue('id') ?></td>
                <td><?= format_date($order->getValue('updatedate')) ?></td>
                <td>&euro; <?= $order->getValue('total') ?></td>
                <td><?= Wildcard::get('shop.order_status_'. $order->getValue('status')) ?></td>
                <td>
                    <a href="<?= rex_getUrl(NULL, NULL, ['order_id' => $order->getValue('id')]) ?>">###label.details###</a>
                </td>
            </tr>
        <?php endforeach; ?>

        </tbody>
    </table>
<?php else: ?>
    ###label.no_orders###
<?php endif; ?>

<?php endif; ?>
