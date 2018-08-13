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


$orders = $this->getVar('orders');

if (count($orders)): ?>
    <table class="table stack">
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
                <td><?= Wildcard::get('shop.order_status_' . $order->getValue('status')) ?></td>
                <td>
                    <a href="<?= rex_getUrl(null, null, [
                        'ctrl'     => 'order.detail',
                        'order_id' => $order->getValue('id'),
                    ]) ?>">###label.details###</a>
                </td>
            </tr>
        <?php endforeach; ?>

        </tbody>
    </table>
<?php else: ?>
    <p class="margin-bottom">
        <i>###simpleshop.no_orders###</i>
    </p>
<?php endif; ?>

