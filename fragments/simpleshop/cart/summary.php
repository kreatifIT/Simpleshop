<?php

namespace FriendsOfREDAXO\Simpleshop;

$config = FragmentConfig::getValue('cart.summary');
$styles = FragmentConfig::getValue('styles');

$discounts      = $this->getVar('discounts', []);
$shipping_costs = $this->getVar('shipping_costs', null);
$total          = $this->getVar('total', 0);
$taxes          = $this->getVar('taxes', []);

?>
<table class="<?= $config['css_class']['table'] ?>" <?= $styles['table'] ?>>

    <tr <?= $styles['tr'] ?>>
        <td <?= $styles['sum-td'] ?>>
            <h3 <?= $styles['h3'] ?>>###simpleshop.brutto_total###</h3>
        </td>
        <td <?= $styles['sum-td-right'] ?>>
            &euro;&nbsp;<?= format_price($total - array_sum($taxes)) ?>
        </td>
    </tr>

    <?php if ($shipping_costs !== null): ?>
        <tr <?= $styles['tr'] ?>>
            <td <?= $styles['sum-td'] ?>>
                <h3 <?= $styles['h3'] ?>>###simpleshop.shipping_costs###</h3>
            </td>
            <td <?= $styles['sum-td-right'] ?>">
            &euro;&nbsp;<?= format_price($shipping_costs) ?>
            </td>
        </tr>
    <?php endif; ?>

    <?php foreach ($discounts as $discount): ?>
        <tr <?= $styles['tr'] ?>>
            <td <?= $styles['sum-td'] ?>>
                <h3 <?= $styles['h3'] ?>><?= $discount['name'] ?></h3>
            </td>
            <td <?= $styles['sum-td-right'] ?>>
                &euro;&nbsp;-<?= format_price($discount['value']) ?>
            </td>
        </tr>
    <?php endforeach; ?>

    <?php foreach ($taxes as $taxVal => $tax): ?>
        <tr <?= $styles['tr'] ?>>
            <td <?= $styles['sum-td'] ?>>
                <h3 <?= $styles['h3'] ?>><?= $taxVal ?>% ###label.tax###</h3>
            </td>
            <td <?= $styles['sum-td-right'] ?>>
                &euro;&nbsp;<?= format_price($tax) ?>
            </td>
        </tr>
    <?php endforeach; ?>

    <!-- total -->
    <tr <?= $styles['tr'] ?>>
        <td <?= $styles['sum-td'] ?>>
            <h2 <?= $styles['h2'] ?>>###label.total_sum###</h2>
        </td>
        <td <?= $styles['sum-td-right'] ?><?= $styles['total'] ?>>
            <div style="font-weight:600;font-size:18px;">&euro;&nbsp;<?= format_price($total) ?></div>
        </td>
    </tr>
</table>