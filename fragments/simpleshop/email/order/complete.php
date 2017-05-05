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

$Order = $this->getVar('Order');
$add_info = $this->getVar('additional_info');
$Payment = $Order->getValue('payment');
$Shipping = $Order->getValue('shipping');

$config = array_merge([
    'is_order_complete'         => true,
    'use_invoicing'             => false,
    'has_image'                 => false,
    'has_remove_button'         => false,
    'has_quantity_control'      => false,
    'has_global_refresh_button' => false,
    'has_edit_link'             => false,
    'email_tpl_styles'          => [
        'body'          => 'border-collapse:collapse;border-spacing:0;padding:0;text-align:left;vertical-align:top;width:100%;',
        'tr'            => 'padding:0;text-align:left;vertical-align:top;',
        'th'            => 'Margin:0;color:#0a0a0a;font-family:Helvetica,Arial,sans-serif;font-size:14px;font-weight:400;line-height:1.6;margin:0;padding:0;text-align:left;',
        'td'            => '-moz-hyphens:auto;-webkit-hyphens:auto;Margin:0;border:1px solid #cacaca;border-collapse:collapse!important;color:#0a0a0a;font-family:Helvetica,Arial,sans-serif;font-size:14px;font-weight:400;hyphens:auto;line-height:1.6;margin:0;padding:10px;text-align:left;vertical-align:top;word-wrap:break-word;',
        'h3'            => 'Margin:0;Margin-bottom:10px;color:inherit;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:400;line-height:1.6;margin:0;padding:0;text-align:left;word-wrap:normal;',
        'p'             => 'Margin:0;Margin-bottom:10px;color:#0a0a0a;font-family:Helvetica,Arial,sans-serif;font-size:14px;font-weight:400;line-height:1.6;margin:0;padding:0;text-align:left;',
        'code'          => 'font-family:Consolas,"Liberation Mono",Courier,monospace;background:#f9f9f9;border:1px solid #cacaca;padding:5px 8px;margin:0;color:#0a0a0a;font-family:Helvetica,Arial,sans-serif;font-size:14px;font-weight:400;line-height:1.6;text-align:left;',
        'callout'       => 'Margin-bottom:16px;border-collapse:collapse;border-spacing:0;margin-bottom:16px;padding:0;text-align:left;vertical-align:top;width:100%;',
        'callout_inner' => 'Margin:0;background:#f3f3f3;border:1px solid #cacaca;color:#0a0a0a;font-family:Helvetica,Arial,sans-serif;font-size:14px;font-weight:400;line-height:1.6;margin:0;padding:10px;text-align:left;width:100%;',
    ],
], $this->getVar('config', []));

$this->setVar('config', $config);
$this->setVar('order', $Order);

?>
    <h2>###shop.email.order_complete_text###</h2>

    <?php

$order_id = $Order->getValue('id');

$this->setVar('tax', $Order->getTaxTotal());
$this->setVar('taxes', $Order->getValue('taxes'));
$this->setVar('total', $Order->getValue('total'));
$this->setVar('initial_total', $Order->getValue('initial_total'));

if ($Shipping) {
    $this->subfragment('simpleshop/shipping/' . $Shipping->plugin_name . '/order_complete.php');
    $this->setVar('shipping_costs', $Order->getValue('shipping_costs'));
}
if ($Payment) {
    $this->subfragment('simpleshop/payment/' . $Payment->plugin_name . '/order_complete.php');
}

$products = $Order->getProducts(false);

?>
<?php if (strlen($add_info)): ?>
    <p><?= $add_info ?></p>
<?php endif; ?>

<?php if ($config['use_invoicing']): ?>
    <div>
        <br/>
        <strong>###company.name###</strong><br/>
        ###company.street###<br/>
        ###company.postal### ###company.location### (###company.province###)<br/>
        ###label.vat_short###: ###company.vat###<br/>
        ###company.shop_invoice_info###
    </div>
    <br/>
<?php endif; ?>

    <h2>###label.order### #<?= $order_id ?></h2>
<?php if ($config['use_invoicing']): ?>
    <h2>###label.invoice_num### <?= $Order->getInvoiceNum() ?></h2>
<?php endif; ?>

    <?php
$this->setVar('invoice_address', $Order->getInvoiceAddress());
$this->setVar('shipping_address', $Shipping ? $Order->getShippingAddress() : null);
$this->subfragment('simpleshop/email/order/address-wrapper.php');
?>


<?php if ($Shipping): ?>
    <!-- shipping -->
    <table class="callout" style="<?= $config['email_tpl_styles']['callout'] ?>">
        <tr style="<?= $config['email_tpl_styles']['tr'] ?>">
            <th class="callout-inner" style="<?= $config['email_tpl_styles']['callout_inner'] ?>">
                <h3>###label.shipment###</h3>
                <p><?= $Shipping->getName() ?></p>
            </th>
            <th class="expander" style="<?= $config['email_tpl_styles']['th'] ?>"></th>
        </tr>
    </table>
<?php endif; ?>

<?php if ($Payment): ?>
    <!-- payment -->
    <table class="callout" style="<?= $config['email_tpl_styles']['callout'] ?>">
        <tr style="<?= $config['email_tpl_styles']['tr'] ?>">
            <th class="callout-inner" style="<?= $config['email_tpl_styles']['callout_inner'] ?>">
                <h3>###label.payment_method###</h3>
                <p><?= $Payment->getName() ?><br/><?= $Payment->getValue('info') ?></p>
            </th>
            <th class="expander" style="<?= $config['email_tpl_styles']['th'] ?>"></th>
        </tr>
    </table>
<?php endif; ?>

    <!-- cart content -->
    <?php
$config['email_tpl_styles'] = array_merge($config['email_tpl_styles'], [
    'body' => $config['email_tpl_styles']['body'] . 'margin-top:20px;',
    'th'   => $config['email_tpl_styles']['th'] . 'background:' . $config['primary_color'] . ';border:1px solid #fff;color:#fff;padding:10px;',
]);
$this->setVar('config', $config);
$this->setVar('products', $products);
$this->subfragment('simpleshop/cart/table-wrapper.php');
?>

    <!-- order conclusion/sum -->
    <?php
$config['email_tpl_styles'] = array_merge($config['email_tpl_styles'], [
    'table' => 'border-collapse:collapse;border-spacing:0;margin-top:20px;padding:0;text-align:left;vertical-align:top;width:100%;',
    'tr'    => 'border-bottom:1px solid #cacaca;padding:0;text-align:left;vertical-align:top;',
    'td'    => '-moz-hyphens:auto;-webkit-hyphens:auto;Margin:0;border-collapse:collapse!important;color:#0a0a0a;font-family:Helvetica,Arial,sans-serif;font-size:14px;font-weight:400;hyphens:auto;line-height:1.6;margin:0;padding:10px;vertical-align:top;word-wrap:break-word;',
    'total' => 'font-size:18px;font-weight:700;',
]);
$this->setVar('config', $config);
$this->subfragment('simpleshop/cart/summary.php');

