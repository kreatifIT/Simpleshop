<?php

/**
 * This file is part of the Kreatif\Project package.
 *
 * @author Kreatif GmbH
 * @author a.platter@kreatif.it
 * Date: 18.03.19
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

$Addon    = rex_addon::get('simplehop');
$Order    = $this->getVar('Order');
$products = $Order->getProducts();

?>
<table class="table table-condensed table-hover products-list">
    <tr class="info">
        <th>
            <a href="javascript:;" onclick="Simpleshop.addOrderProduct(this, <?= $Order->getId() ?>);"><i class="rex-icon rex-icon-add"></i></a>
        </th>
        <th>Code</th>
        <th><?= $Addon->i18n('label.product') ?></th>
        <th><?= $Addon->i18n('label.amount') ?></th>
        <th><?= $Addon->i18n('label.net_single_price') ?></th>
        <th>&nbsp;</th>
    </tr>

    <?php foreach ($products as $index => $order_product): ?>
        <?php
        $Product = $order_product->getValue('data');
        ?>
        <tr>
            <td class="text-center"><?= $index + 1 ?></td>
            <td><?= $Product->getValue('code') ?></td>
            <td><?= $Product->getName() ?></td>
            <td><input type="text" name="quantity" value="<?= $Product->getValue('cart_quantity') ?>" size="8" class="text-center form-control" onkeyup="Simpleshop.changeOrderProductQuantity(this, <?= $Order->getId() ?>, '<?= $order_product->getId() ?>', '<?= $Product->getValue('cart_quantity') ?>');"></td>
            <td><?= format_price($Product->getPrice(false)) ?> &euro;</td>
            <td>
                <a href="javascript:;" onclick="return Simpleshop.removeOrderProduct(this, <?= $Order->getId() ?>, '<?= $order_product->getId() ?>', '<?= $Product->getValue('cart_quantity') ?>', '<?= $Addon->i18n('label.really_delete_entry') ?>');">
                    <i class="rex-icon rex-icon-delete"></i>
                    <?= $Addon->i18n('action.remove') ?>
                </a>
            </td>
        </tr>
    <?php endforeach; ?>
</table>

<?php if (rex_request::isXmlHttpRequest()): ?>
    <a href="<?= rex_url::backendPage('yform/manager/data_edit', array_merge([
        'data_id'    => $Order->getId(),
        'table_name' => \FriendsOfREDAXO\Simpleshop\Order::TABLE,
        'func'       => 'edit',
    ], ['ss-action' => 'recalculate_sums', 'ts' => time()])) ?>" class="btn btn-primary hiddens">Gesamtsumme neu berechnen</a>
<?php endif; ?>