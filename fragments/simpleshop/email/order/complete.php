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

$Order         = $this->getVar('Order');
$add_info      = $this->getVar('additional_info');
$primary_color = $this->getVar('primary_color');
$Payment       = $Order->getValue('payment');
$Shipping      = $Order->getValue('shipping');
$products      = $Order->getProducts(false);

$styles = FragmentConfig::getValue('email_styles');
$config = FragmentConfig::getValue('checkout.email');
//$this->setVar('tax', $Order->getTaxTotal());
//$this->setVar('taxes', $Order->getValue('taxes'));
//$this->setVar('total', $Order->getValue('total'));
//$this->setVar('initial_total', $Order->getValue('initial_total'));

?>
<div class="order-complete-email">
    <h2>###simpleshop.email.order_complete_text###</h2>

    <?php
    if ($Shipping) {
        $this->subfragment('simpleshop/shipping/' . $Shipping->plugin_name . '/order_complete.php');
        $this->setVar('shipping_costs', $Order->getValue('shipping_costs'));
    }
    if ($Payment) {
        $this->subfragment('simpleshop/payment/' . $Payment->plugin_name . '/order_complete.php');
    }
    ?>

    <?php if (strlen($add_info)): ?>
        <p><?= $add_info ?></p>
    <?php endif; ?>

    <?php
    if ($config['use_invoicing']) {
        $this->subfragment('simpleshop/email/order/invoice_data.php');
    }
    ?>

    <h2>###label.order### #<?= $Order->getValue('id') ?></h2>

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
        <table class="callout" style="<?= $styles['callout'] ?>">
            <tr style="<?= $styles['tr'] ?>">
                <th class="callout-inner" style="<?= $styles['callout_inner'] ?>">
                    <h3>###label.shipment###</h3>
                    <p><?= $Shipping->getName() ?></p>
                </th>
                <th class="expander" style="<?= $styles['th'] ?>"></th>
            </tr>
        </table>
    <?php endif; ?>

    <?php if ($Payment): ?>
        <!-- payment -->
        <table class="callout" style="<?= $styles['callout'] ?>">
            <tr style="<?= $styles['tr'] ?>">
                <th class="callout-inner" style="<?= $styles['callout_inner'] ?>">
                    <h3>###label.payment_method###</h3>
                    <p><?= $Payment->getName() ?><br/><?= $Payment->getValue('info') ?></p>
                </th>
                <th class="expander" style="<?= $styles['th'] ?>"></th>
            </tr>
        </table>
    <?php endif; ?>

    <!-- cart content -->
    <?php
//    $styles = array_merge($styles, [
//        'body' => $styles['body'] . 'margin-top:20px;',
//        'th'   => $styles['th'] . 'background:' . $primary_color . ';border:1px solid #fff;color:#fff;padding:10px;',
//    ]);
    $this->setVar('products', $products);
    $this->subfragment('simpleshop/cart/table-wrapper.php');
    ?>

    <!-- order conclusion/sum -->
    <?php
//    $styles = array_merge($styles, [
//        'table' => 'border-collapse:collapse;border-spacing:0;margin-top:20px;padding:0;text-align:left;vertical-align:top;width:100%;',
//        'tr'    => 'border-bottom:1px solid #cacaca;padding:0;text-align:left;vertical-align:top;',
//        'td'    => '-moz-hyphens:auto;-webkit-hyphens:auto;Margin:0;border-collapse:collapse!important;color:#0a0a0a;font-family:Helvetica,Arial,sans-serif;font-size:14px;font-weight:400;hyphens:auto;line-height:1.6;margin:0;padding:10px;vertical-align:top;word-wrap:break-word;',
//        'total' => 'font-size:18px;font-weight:700;',
//    ]);
    $this->subfragment('simpleshop/cart/summary.php');

    ?>
</div>
