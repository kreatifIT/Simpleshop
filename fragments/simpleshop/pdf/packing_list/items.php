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

$type     = $this->getVar('type');
$Order    = $this->getVar('Order');
$products = (array)$Order->getOrderProducts();

if (count($products) == 0) {
    return;
}

?>
<table id="packing-items" width="100%" cellpadding="6">
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
            <td width="8%" align="center"><?= $index + 1 ?></td>
            <td width="14%" align="center"><?= $code ?></td>
            <td width="38%" class="product-name">
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
            <td width="10%" align="center"><?= $quantity ?></td>
            <td width="15%" align="right">&euro; <?= format_price($product->getPrice($type != 'invoice')) ?></td>
            <td width="15%" align="right">&euro; <?= format_price($product->getPrice($type != 'invoice') * $quantity) ?></td>
        </tr>
    <?php endforeach; ?>

    </tbody>
</table>
