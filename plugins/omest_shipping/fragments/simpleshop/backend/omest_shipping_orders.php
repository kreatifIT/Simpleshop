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


$Addon     = $this->getVar('Addon');
$orders    = $this->getVar('orders');
$order_ids = $this->getVar('order_ids');

$pallet_types = [
    '80x60',
    '80x80',
    '80x120',
    '100x100',
    '100x120',
    '150x120',
    '200x120',
];

?>
<fieldset>

    <p><?= $Addon->i18n('omest_shipping.orders_info_text'); ?></p>

    <table class="table">
        <tr>
            <th></th>
            <th><?= $Addon->i18n('label.order_no'); ?></th>
            <th><?= $Addon->i18n('label.customer'); ?></th>
            <th><?= $Addon->i18n('label.order_sum'); ?></th>
            <th><?= $Addon->i18n('omest_shipping.is_pallett'); ?></th>
            <th><?= $Addon->i18n('omest_shipping.size_info'); ?></th>
            <th><?= $Addon->i18n('omest_shipping.weight_info'); ?></th>
            <th></th>
        </tr>

        <tbody class="table-hover">
        <?php if (count($orders)): ?>
            <?php foreach ($orders as $order):
                $Address = $order->getShippingAddress();
                $Shipping = $order->getValue('shipping');
                $parcels = $Shipping->getValue('parcels');

                if (!($Shipping instanceof Omest) && !($Shipping instanceof DefaultShipping) || $Shipping->getValue('shipping_key') != '') {
                    continue;
                }

                if (count($parcels) == 0) {
                    $parcels[] = new Parcel();
                }
                ?>
                <tr>
                    <td><input type="checkbox" name="orders[]" value="<?= $order->getValue('id') ?>" <?= (empty($order_ids) || in_array($order->getValue('id'), $order_ids)) ? 'checked="checked"' : '' ?>/></td>
                    <td><?= $order->getValue('id') ?></td>
                    <td><?= $Address->getName() ?></td>
                    <td><?= $order->getValue('total') ?></td>
                    <td>
                        <?php foreach ($parcels as $parcel_index => $parcel): ?>
                            <div class="pallett form-group">
                                <select name="prop[<?= $order->getId() ?>][<?= $parcel_index ?>][pallett]" class="form-control">
                                    <option value="">- keine Pallette -</option>
                                    <?php foreach ($pallet_types as $pallet_type): ?>
                                        <option value="<?= $pallet_type ?>" <?= $parcel->getValue('pallett') == $pallet_type ? 'selected="selected"' : '' ?>><?= $pallet_type ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        <?php endforeach; ?>
                    </td>
                    <td>
                        <?php foreach ($parcels as $parcel_index => $parcel): ?>
                            <div class="dimensions">
                                <input type="text" size="8" name="prop[<?= $order->getId() ?>][<?= $parcel_index ?>][length]" value="<?= $parcel->getValue('length') ?>">
                                <input type="text" size="8" name="prop[<?= $order->getId() ?>][<?= $parcel_index ?>][width]" value="<?= $parcel->getValue('width') ?>">
                                <input type="text" size="8" name="prop[<?= $order->getId() ?>][<?= $parcel_index ?>][height]" value="<?= $parcel->getValue('height') ?>">
                            </div>
                        <?php endforeach; ?>
                    </td>
                    <td>
                        <?php foreach ($parcels as $parcel_index => $parcel): ?>
                            <div class="weights">
                                <input type="text" size="12" name="prop[<?= $order->getId() ?>][<?= $parcel_index ?>][weight]" value="<?= $parcel->getValue('weight') ?>">
                            </div>
                        <?php endforeach; ?>
                    </td>
                    <td>
                        <a href="#" onclick="return SimpleshopBackend.addShippingPackage(this);"><?= $Addon->i18n('omest_shipping.add_package'); ?></a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="10" class="text-center" style="padding:30px 0;"><em><?= $Addon->i18n('omest_shipping.no_orders_to_send'); ?></em></td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>


    <!--    <br/>-->
    <!---->
    <!--    <legend>--><? //= $Addon->i18n('url_settings');
    ?><!--</legend>-->
    <!--    <p>--><? //= $Addon->i18n('setup_column_creating_text');
    ?><!--</p>-->
    <!--    <div class="row">-->
    <!--        <div class="col-sm-12">-->
    <!--            <div class="rex-select-style">-->
    <!--                --><? //= \rex_var_linklist::getWidget(1, 'test', '')
    ?>
    <!--            </div>-->
    <!--        </div>-->
    <!--    </div>-->

</fieldset>