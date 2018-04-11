<?php

namespace FriendsOfREDAXO\Simpleshop;

$config = FragmentConfig::getValue('cart.summary');
$styles = FragmentConfig::getValue('styles');

$Order       = $this->getVar('Order');
$shipping    = $Order->getValue('shipping_costs');
$brut_prices = $Order->getValue('brut_prices');
$discount    = $Order->getValue('discount');
$total       = $Order->getValue('total');
$taxes       = $Order->getValue('taxes');
$promotions  = (array) $Order->getValue('promotions');

?>
<table class="<?= $config['css_class']['table'] ?>" <?= $styles['table'] ?>>

    <tr <?= $styles['tr'] ?>>
        <td <?= $styles['sum-td'] ?>>
            <h3 <?= $styles['h3'] ?>>###simpleshop.subtotal###</h3>
        </td>
        <td <?= $styles['sum-td-right'] ?>>
            &euro;&nbsp;<?= format_price(array_sum($brut_prices)) ?>
        </td>
    </tr>

    <?php foreach ($promotions as $promotion):
        if (!is_object($promotion) || $promotion->getValue('value') == 0) {
            continue;
        }
        ?>
        <tr <?= $styles['tr'] ?>>
            <td <?= $styles['sum-td'] ?>>
                <h3 <?= $styles['h3'] ?>><?= $promotion->getName() ?></h3>
            </td>
            <td <?= $styles['sum-td-right'] ?>>
                &euro;&nbsp;-<?= format_price($promotion->getValue('value')) ?>
            </td>
        </tr>
    <?php endforeach; ?>

    <?php if ($Order->getValue('shipping')): ?>
        <tr <?= $styles['tr'] ?>>
            <td <?= $styles['sum-td'] ?>>
                <h3 <?= $styles['h3'] ?>>+ ###simpleshop.shipping_costs###</h3>
            </td>
            <td <?= $styles['sum-td-right'] ?>">
            &euro;&nbsp;<?= format_price($shipping) ?>
            </td>
        </tr>
    <?php endif; ?>

    <?php if (!$Order->isTaxFree()): ?>
        <tr <?= $styles['tr'] ?>>
            <td <?= $styles['sum-td'] ?>>
                <h3 <?= $styles['h3'] ?>>###simpleshop.brutto_total###</h3>
            </td>
            <td <?= $styles['sum-td-right'] ?>">
            &euro;&nbsp;<?= format_price(array_sum($brut_prices) + $shipping - $discount) ?>
            </td>
        </tr>

        <?php foreach ($taxes as $percent => $tax): ?>
            <tr <?= $styles['tr'] ?>>
                <td <?= $styles['sum-td'] ?>>
                    <h3 <?= $styles['h3'] ?>><?= $percent ?>% ###label.tax###</h3>
                </td>
                <td <?= $styles['sum-td-right'] ?>>
                    &euro;&nbsp;<?= format_price($tax) ?>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php endif; ?>

    <!-- total -->
    <tr <?= $styles['tr'] ?>>
        <td <?= $styles['sum-td'] ?>>
            <h2 <?= $styles['h2'] ?>>###label.total_sum###</h2>
        </td>
        <td <?= $styles['sum-td-right'] ?><?= $styles['total'] ?>>
            <div style="font-weight:600;font-size:18px;">
                &euro;&nbsp;<?= format_price($total) ?>
            </div>
        </td>
    </tr>
</table>