<?php

/**
 * This file is part of the Kreatif\Project package.
 *
 * @author Kreatif GmbH
 * @author a.platter@kreatif.it
 * Date: 05.06.18
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

$Order    = $this->getVar('Order');
$products = (array)$Order->getOrderProducts();
$config   = \FriendsOfREDAXO\Simpleshop\FragmentConfig::getValue('checkout');

if (count($products) == 0) {
    return;
}

?>
<table id="invoice-items" width="100%" cellpadding="8">
    <thead>
    <tr>
        <td width="8%">###label.position_short###</td>
        <td width="14%">###label.code###</td>
        <td width="38%">###label.description###</td>
        <td width="10%">###label.amount###</td>
        <td width="15%">###label.single_price###</td>
        <td width="15%">###label.total_sum###</td>
    </tr>
    </thead>
    <tbody>
    <!-- ITEMS HERE -->
    <?php foreach ($products as $index => $product):
        $quantity = $product->getValue('cart_quantity');
        $code = $product->valueIsset('code') ? $product->getValue('code') : $product->getId();
        $features = $product->getValue('features');
        $discount = $product->getValue('discount');
        $name = rex_extension::registerPoint(new rex_extension_point('simpleshop.pdfInvoice.itemName', $product->getName(), [
            'product' => $product,
        ]));
        ?>
        <tr>
            <td align="center"><?= $index + 1 ?></td>
            <td align="center"><?= $code ?></td>
            <td class="product-name">
                <?= $name ?>
                <?php if (count($features)): ?>
                    <?php foreach ($features as $feature): ?>
                        <div class="feature"><?= $feature->getName() ?></div>
                    <?php endforeach; ?>
                <?php endif; ?>
                <?php if ($discount): ?>
                    <div class="product-discount"><?= $discount->getName() ?></div>
                <?php endif; ?>
            </td>
            <td align="center"><?= $quantity ?></td>
            <td align="right">&euro; <?= format_price($product->getPrice($config['show_tax_info'])) ?></td>
            <td align="right">&euro; <?= format_price($product->getPrice($config['show_tax_info']) * $quantity) ?></td>
        </tr>
    <?php endforeach; ?>

    </tbody>
</table>
