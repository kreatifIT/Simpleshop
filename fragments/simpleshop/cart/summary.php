<?php

namespace FriendsOfREDAXO\Simpleshop;

$config = FragmentConfig::getValue('cart.summary');
$styles = FragmentConfig::getValue('styles');

$discounts      = $this->getVar('discounts', []);
$shipping_costs = $this->getVar('shipping_costs', null);
$initial_total  = $this->getVar('initial_total', 0);
$total          = $this->getVar('total', 0);
$tax            = $this->getVar('tax', null);

?>
<table class="<?= $config['css_class']['table'] ?>" <?= $styles['table'] ?>>

    <tr <?= $styles['tr'] ?>>
        <td <?= $styles['td'] ?>>###simpleshop.brutto_total###</td>
        <td class="text-right" <?= $styles['td'] ?>>
            &euro; <?= format_price($initial_total) ?>
        </td>
    </tr>

    <?php if ($shipping_costs !== null): ?>
        <tr <?= $styles['tr'] ?>>
            <td <?= $styles['td'] ?>>###label.shipment_cost###</td>
            <td class="text-right" <?= $styles['td'] ?>">
            &euro; <?= format_price($shipping_costs) ?>
            </td>
        </tr>
    <?php endif; ?>

    <?php foreach ($discounts as $discount): ?>
        <tr <?= $styles['tr'] ?>>
            <td <?= $styles['td'] ?>><?= $discount['name'] ?></td>
            <td class="text-right" <?= $styles['td'] ?>>
                &euro; -<?= format_price($discount['value']) ?>
            </td>
        </tr>
    <?php endforeach; ?>

    <?php if ($tax !== null): ?>
        <tr <?= $styles['tr'] ?>>
            <td <?= $styles['td'] ?>>###label.tax_included###</td>
            <td class="text-right" <?= $styles['td'] ?>>
                &euro; <?= format_price($tax) ?>
            </td>
        </tr>
    <?php endif; ?>

    <!-- total -->
    <tr <?= $styles['tr'] ?>>
        <td <?= $styles['td'] ?>>###label.total_sum###</td>
        <td class="text-right" <?= $styles['td'] ?><?= $styles['total'] ?>>
            &euro; <?= format_price($total) ?>
        </td>
    </tr>
</table>