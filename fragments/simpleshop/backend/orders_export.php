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

$Addon      = $this->getVar('Addon');
$Settings   = $this->getVar('Settings');
$orders     = $this->getVar('orders');
$statuses   = $this->getVar('statuses');
$useInvoice = from_array($Settings, 'use_invoicing') == 1;

?>
<fieldset>

    <p><?= $Addon->i18n('label.orders_export_info_text'); ?></p>

    <table class="table">
        <tr>
            <th></th>
            <?php if ($useInvoice): ?>
                <th><?= $Addon->i18n('label.invoice_num'); ?></th>
            <?php endif; ?>
            <th><?= $Addon->i18n('label.order_no'); ?></th>
            <th><?= $Addon->i18n('label.customer'); ?></th>
            <th><?= $Addon->i18n('label.order_sum'); ?></th>
            <th><?= $Addon->i18n('status'); ?></th>
        </tr>

        <tbody class="table-hover">
        <?php if (count($orders)): ?>
            <?php foreach ($orders as $order):
                $user_id = $order->getValue('customer_id');
                $Customer = $user_id ? Customer::get($user_id) : $order->getValue('address_1');
                ?>
                <tr>
                    <td><input type="checkbox" name="orders[]" value="<?= $order->getValue('id') ?>" checked="checked"/></td>
                    <?php if ($useInvoice): ?>
                        <td><?= $order->getValue('invoice_num'); ?></td>
                    <?php endif; ?>
                    <td><?= $order->getValue('id') ?></td>
                    <td><?= $Customer ? $Customer->getName() : '<i>unknown</i>' ?></td>
                    <td><?= format_price($order->getValue('total')) ?></td>
                    <td><?= $statuses[$order->getValue('status')] ?: $order->getValue('status') ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="10" class="text-center" style="padding:30px 0;"><em><?= $Addon->i18n('label.no_orders_export'); ?></em></td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>

</fieldset>