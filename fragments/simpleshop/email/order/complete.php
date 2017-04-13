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

$Order    = $this->getVar('Order');
$add_info = $this->getVar('additional_info');
$Payment  = $Order->getValue('payment');
$Shipping = $Order->getValue('shipping');

$this->setVar('order', $Order);

?>
<h2>###shop.email.order_complete_text###</h2>

<?php

if ($Shipping) {
    $this->subfragment('simpleshop/shipping/' . $Shipping->plugin_name . '/order_complete.php');
}
if ($Payment) {
    $this->subfragment('simpleshop/payment/' . $Payment->plugin_name . '/order_complete.php');
}

if (strlen($add_info)) {
    echo "<p>{$add_info}</p>";
}

$order_id   = $Order->getValue('id');
$address_1  = $Order->getValue('address_1');
$address_2  = $Order->getValue('address_2');
$promotions = $Order->getValue('promotions');
$extras     = $Order->getValue('extras');

$products  = [];
$_products = OrderProduct::query()->where('order_id', $order_id)->find();

foreach ($_products as $product) {
    $_product = $product->getValue('data');
    $_product->setValue('cart_quantity', $product->getValue('quantity'));
    $_product->setValue('code', $product->getValue('code'));
    $products[] = $_product;
}

?>

<h2>###label.order### #<?= $order_id ?></h2>
<!-- address -->
<table class="callout"
       style="Margin-bottom:16px;border-collapse:collapse;border-spacing:0;margin-bottom:16px;padding:0;text-align:left;vertical-align:top;width:100%">
    <tr style="padding:0;text-align:left;vertical-align:top">
        <th class="callout-inner"
            style="Margin:0;background:#f3f3f3;border:1px solid #cacaca;color:#0a0a0a;font-family:Helvetica,Arial,sans-serif;font-size:14px;font-weight:400;line-height:1.6;margin:0;padding:10px;text-align:left;width:100%">
            <table class="row"
                   style="border-collapse:collapse;border-spacing:0;display:table;padding:0;position:relative;text-align:left;vertical-align:top;width:100%">
                <tbody>
                <tr style="padding:0;text-align:left;vertical-align:top">
                    <th class="small-12 large-6 columns first"
                        style="Margin:0 auto;color:#0a0a0a;font-family:Helvetica,Arial,sans-serif;font-size:14px;font-weight:400;line-height:1.6;margin:0 auto;padding:0;padding-bottom:16px;padding-left:0!important;padding-right:0!important;text-align:left;width:50%">
                        <table style="border-collapse:collapse;border-spacing:0;padding:0;text-align:left;vertical-align:top;width:100%">
                            <tr style="padding:0;text-align:left;vertical-align:top">
                                <th style="Margin:0;color:#0a0a0a;font-family:Helvetica,Arial,sans-serif;font-size:14px;font-weight:400;line-height:1.6;margin:0;padding:0;text-align:left">
                                    <?php
                                    $this->setVar('address', $address_1);
                                    $this->setVar('title', '###shop.invoice_address###');
                                    $this->setVar('has_edit_link', false);
                                    $this->subfragment('simpleshop/checkout/summary/address_item.php');
                                    ?>
                                </th>
                            </tr>
                        </table>
                    </th>
                    <?php if ($Shipping): ?>
                        <th class="small-12 large-6 columns last"
                            style="Margin:0 auto;color:#0a0a0a;font-family:Helvetica,Arial,sans-serif;font-size:14px;font-weight:400;line-height:1.6;margin:0 auto;padding:0;padding-bottom:16px;padding-left:0!important;padding-right:0!important;text-align:left;width:50%">
                            <table style="border-collapse:collapse;border-spacing:0;padding:0;text-align:left;vertical-align:top;width:100%">
                                <tr style="padding:0;text-align:left;vertical-align:top">
                                    <th style="Margin:0;color:#0a0a0a;font-family:Helvetica,Arial,sans-serif;font-size:14px;font-weight:400;line-height:1.6;margin:0;padding:0;text-align:left">
                                        <?php
                                        if ($extras['address_extras']['use_shipping_address']) {
                                            $this->setVar('address', $address_2);
                                        }
                                        else {
                                            $this->setVar('address', $address_1);
                                        }
                                        $this->setVar('title', '###shop.shipping_address###');
                                        $this->setVar('has_edit_link', false);
                                        $this->subfragment('simpleshop/checkout/summary/address_item.php');
                                        ?>
                                    </th>
                                </tr>
                            </table>
                        </th>
                    <?php endif; ?>
                </tr>
                </tbody>
            </table>
        </th>
        <th class="expander"
            style="Margin:0;color:#0a0a0a;font-family:Helvetica,Arial,sans-serif;font-size:14px;font-weight:400;line-height:1.6;margin:0;padding:0!important;text-align:left;visibility:hidden;width:0"></th>
    </tr>
</table>
<?php if ($Shipping): ?>
    <!-- shipping -->
    <table class="callout"
           style="Margin-bottom:16px;border-collapse:collapse;border-spacing:0;margin-bottom:16px;padding:0;text-align:left;vertical-align:top;width:100%">
        <tr style="padding:0;text-align:left;vertical-align:top">
            <th class="callout-inner"
                style="Margin:0;background:#f3f3f3;border:1px solid #cacaca;color:#0a0a0a;font-family:Helvetica,Arial,sans-serif;font-size:14px;font-weight:400;line-height:1.6;margin:0;padding:10px;text-align:left;width:100%">
                <h3>###label.shipment###</h3>
                <p><?= $Shipping->getName() ?></p>
            </th>
            <th class="expander"
                style="Margin:0;color:#0a0a0a;font-family:Helvetica,Arial,sans-serif;font-size:14px;font-weight:400;line-height:1.6;margin:0;padding:0!important;text-align:left;visibility:hidden;width:0"></th>
        </tr>
    </table>
<?php endif; ?>
<?php if ($Payment): ?>
    <!-- payment -->
    <table class="callout"
           style="Margin-bottom:16px;border-collapse:collapse;border-spacing:0;margin-bottom:16px;padding:0;text-align:left;vertical-align:top;width:100%">
        <tr style="padding:0;text-align:left;vertical-align:top">
            <th class="callout-inner"
                style="Margin:0;background:#f3f3f3;border:1px solid #cacaca;color:#0a0a0a;font-family:Helvetica,Arial,sans-serif;font-size:14px;font-weight:400;line-height:1.6;margin:0;padding:10px;text-align:left;width:100%">
                <h3>###label.payment###</h3>
                <p><?= $Payment->getName() ?><br/><?= $Payment->getValue('info') ?></p>
            </th>
            <th class="expander"
                style="Margin:0;color:#0a0a0a;font-family:Helvetica,Arial,sans-serif;font-size:14px;font-weight:400;line-height:1.6;margin:0;padding:0!important;text-align:left;visibility:hidden;width:0"></th>
        </tr>
    </table>
<?php endif; ?>

<!-- cart content -->
<?php

$cart_table_styles = [
    'table' => 'border-collapse:collapse;border-spacing:0;margin-top:20px;padding:0;text-align:left;vertical-align:top;width:100%;',
    'tr'    => 'padding:0;text-align:left;vertical-align:top;',
    'th'    => 'Margin:0;background:#00913e;border:1px solid #cacaca;color:#fefefe;font-family:Helvetica,Arial,sans-serif;font-size:14px;font-weight:400;line-height:1.6;margin:0;padding:10px;text-align:left;',
];

$cart_item_styles = [
    'tr'   => 'padding:0;text-align:left;vertical-align:top',
    'td'   => '-moz-hyphens:auto;-webkit-hyphens:auto;Margin:0;border:1px solid #cacaca;border-collapse:collapse!important;color:#0a0a0a;font-family:Helvetica,Arial,sans-serif;font-size:14px;font-weight:400;hyphens:auto;line-height:1.6;margin:0;padding:10px;text-align:left;vertical-align:top;word-wrap:break-word',
    'h3'   => 'Margin:0;Margin-bottom:10px;color:inherit;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:400;line-height:1.6;margin:0;padding:0;text-align:left;word-wrap:normal',
    'p'    => 'Margin:0;Margin-bottom:10px;color:#0a0a0a;font-family:Helvetica,Arial,sans-serif;font-size:14px;font-weight:400;line-height:1.6;margin:0;padding:0;text-align:left',
    'code' => 'font-family:Consolas,"Liberation Mono",Courier,monospace;background:#f9f9f9;border:1px solid #cacaca;padding:5px 8px;margin:0;color:#0a0a0a;font-family:Helvetica,Arial,sans-serif;font-size:14px;font-weight:400;line-height:1.6;text-align:left',
];
?>

<table style="<?= $cart_table_styles['table']; ?>">
    <thead>
    <tr style="<?= $cart_table_styles['tr']; ?>">
        <th style="<?= $cart_table_styles['th']; ?>">###shop.produt_code###</th>
        <th style="<?= $cart_table_styles['th']; ?>">###shop.single_price###</th>
        <th style="<?= $cart_table_styles['th']; ?>">###shop.amount###</th>
        <th style="<?= $cart_table_styles['th']; ?>">###label.total###</th>
    </tr>
    </thead>
    <tbody>
    <?php
    foreach ($products as $product) {
        $this->setVar('product', $product);
        $this->setVar('has_quantity_control', false);
        $this->setVar('has_remove_button', false);
        $this->setVar('has_image', false);
        $this->setVar('email_tpl_styles', $cart_item_styles);
        echo $this->subfragment('simpleshop/product/general/cart/item.php');
    }
    ?>
    </tbody>
</table>

<!-- order conclusion/sum -->
<?php

$sum_table_styles = [
    'table' => 'border-collapse:collapse;border-spacing:0;margin-top:20px;padding:0;text-align:left;vertical-align:top;width:100%;',
    'tr'    => 'border-bottom:1px solid #cacaca;padding:0;text-align:left;vertical-align:top;',
    'td'    => '-moz-hyphens:auto;-webkit-hyphens:auto;Margin:0;border-collapse:collapse!important;color:#0a0a0a;font-family:Helvetica,Arial,sans-serif;font-size:14px;font-weight:400;hyphens:auto;line-height:1.6;margin:0;padding:10px;text-align:left;vertical-align:top;word-wrap:break-word;',
    'total' => 'font-size:18px;font-weight:700;',
];

$taxes          = $Order->getValue('tax');
$shipping_costs = $Order->getValue('initial_shipping_costs');
$subtotal       = $Order->getValue('initial_total');
$total          = $Order->getValue('total');

?>
<table style="<?= $sum_table_styles['table'] ?>">
    <?php if ($subtotal != $total): ?>
        <tr style="<?= $sum_table_styles['tr'] ?>">
            <td style="<?= $sum_table_styles['td'] ?>">###label.subtotal###</td>
            <td style="<?= $sum_table_styles['td'] ?>text-align:right;">
                &euro; <?= format_price($subtotal) ?>
            </td>
        </tr>
    <?php endif; ?>
    <?php if ($shipping_costs): ?>
        <tr style="<?= $sum_table_styles['tr'] ?>">
            <td style="<?= $sum_table_styles['td'] ?>">###label.shipment_cost###</td>
            <td style="<?= $sum_table_styles['td'] ?>text-align:right;">
                &euro; <?= format_price($shipping_costs) ?>
            </td>
        </tr>
    <?php endif; ?>
    <?php foreach ($taxes as $tax_perc => $tax): ?>
        <tr style="<?= $sum_table_styles['tr'] ?>">
            <td style="<?= $sum_table_styles['td'] ?>">###label.tax_included### <?= $tax_perc ?>%</td>
            <td style="<?= $sum_table_styles['td'] ?>text-align:right;">
                &euro; <?= format_price($tax) ?>
            </td>
        </tr>
    <?php endforeach; ?>
    <?php foreach ($promotions as $promotion):
        $percent = $promotion->getValue('discount_percent');
        $_value = $promotion->getValue('discount_value');
        $value = $percent ? $percent . '%' : ($_value > $total ? $total : $_value);
        ?>
        <?php if ($value != 0): ?>
        <tr style="<?= $sum_table_styles['tr'] ?>">
            <td style="<?= $sum_table_styles['td'] ?>"><?= $promotion->getName() ?></td>
            <td style="<?= $sum_table_styles['td'] ?>text-align:right;">
                &euro; -<?= format_price($value) ?>
            </td>
        </tr>
    <?php endif; ?>
    <?php endforeach; ?>
    <!-- total -->
    <tr style="<?= $sum_table_styles['tr'] ?>">
        <td style="<?= $sum_table_styles['td'] . $sum_table_styles['total'] ?>">###label.total_sum###</td>
        <td style="<?= $sum_table_styles['td'] . $sum_table_styles['total'] ?>text-align:right;">
            &euro; <?= format_price($total) ?>
        </td>
    </tr>
</table>

