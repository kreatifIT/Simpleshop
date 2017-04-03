<?php


$config = $this->getVar('config', []);
$styles = array_merge([
    'css'   => '',
    'table' => '',
    'tr'    => '',
    'td'    => '',
    'total' => '',
], (array) $config['email_tpl_styles']);

    $discounts      = $this->getVar('discounts', []);
$shipping_costs = $this->getVar('shipping_costs', null);
$subtotal       = $this->getVar('subtotal', 0);
$total          = $this->getVar('total', 0);
$tax            = $this->getVar('tax', null);

?>
<table class="<?= $styles['css'] ?>" style="<?= $styles['table'] ?>">

    <?php if ($subtotal != $total): ?>
        <tr <?= $styles['tr'] ? 'style="' . $styles['tr'] . '"' : '' ?>>
            <td <?= $styles['td'] ? 'style="' . $styles['td'] . '"' : '' ?>>###label.subtotal###</td>
            <td class="text-right" <?= $styles['td'] ? 'style="text-align:right;' . $styles['td'] . '"' : '' ?>>
                &euro; <?= format_price($subtotal) ?>
            </td>
        </tr>
    <?php endif; ?>

    <?php if ($shipping_costs !== null): ?>
        <tr <?= $styles['tr'] ? 'style="' . $styles['tr'] . '"' : '' ?>>
            <td <?= $styles['td'] ? 'style="' . $styles['td'] . '"' : '' ?>>###label.shipment_cost###</td>
            <td class="text-right" <?= $styles['td'] ? 'style="text-align:right;' . $styles['td'] . '"' : '' ?>">
            &euro; <?= format_price($shipping_costs) ?>
            </td>
        </tr>
    <?php endif; ?>

    <?php foreach ($discounts as $discount): ?>
        <tr <?= $styles['tr'] ? 'style="' . $styles['tr'] . '"' : '' ?>>
            <td <?= $styles['td'] ? 'style="' . $styles['td'] . '"' : '' ?>><?= $discount['name'] ?></td>
            <td class="text-right" <?= $styles['td'] ? 'style="text-align:right;' . $styles['td'] . '"' : '' ?>>
                &euro; -<?= format_price($discount['value']) ?>
            </td>
        </tr>
    <?php endforeach; ?>

    <?php if ($tax !== null): ?>
        <tr <?= $styles['tr'] ? 'style="' . $styles['tr'] . '"' : '' ?>>
            <td <?= $styles['td'] ? 'style="' . $styles['td'] . '"' : '' ?>>###label.tax_included###</td>
            <td class="text-right" <?= $styles['td'] ? 'style="text-align:right;' . $styles['td'] . '"' : '' ?>>
                &euro; <?= format_price($tax) ?>
            </td>
        </tr>
    <?php endif; ?>

    <!-- total -->
    <tr <?= $styles['tr'] ? 'style="' . $styles['tr'] . '"' : '' ?>>
        <td <?= $styles['td'] ? 'style="' . $styles['td'] . $styles['total'] . '"' : '' ?>>###label.total_sum###</td>
        <td class="text-right" <?= $styles['td'] ? 'style="text-align:right;' . $styles['td'] . $styles['total'] . '"' : '' ?>>
            &euro; <?= format_price($total) ?>
        </td>
    </tr>
</table>