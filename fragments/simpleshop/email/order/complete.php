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
$Payment       = $Order->getValue('payment');
$Shipping      = $Order->getValue('shipping');
$products      = $Order->getProducts(false);
$Settings      = \rex::getConfig('simpleshop.Settings');


FragmentConfig::$data['cart']['has_remove_button'] = false;
FragmentConfig::$data['cart']['button']['has_quantity_control'] = false;
FragmentConfig::$data['cart']['table-wrapper']['has_go_ahead'] = false;

$styles = FragmentConfig::getValue('email_styles');

?>
<div class="order-complete-email">
    <h2>###simpleshop.email.order_complete_text###</h2>

    <?php
    if ($Payment) {
        $this->subfragment('simpleshop/payment/' . $Payment->plugin_name . '/order_complete.php');
    }
    if ($Shipping) {
        $this->subfragment('simpleshop/shipping/' . $Shipping->plugin_name . '/order_complete.php');

        if ($Shipping->hasCosts()) {
            $this->setVar('shipping_costs', $Order->getValue('shipping_costs'));
        }
    }
    ?>

    <?php if (strlen($add_info)): ?>
        <p><?= $add_info ?></p>
    <?php endif; ?>

    <h2>###label.order### #<?= $Order->getValue('id') ?></h2>

    <?php if ($Settings['use_invoicing']): ?>
        <h2>###label.invoice_num### <?= $Order->getInvoiceNum() ?></h2>
    <?php endif; ?>

    <?php
    $this->setVar('invoice_address', $Order->getInvoiceAddress());
    $this->setVar('shipping_address', $Shipping ? $Order->getShippingAddress() : null);
    $this->subfragment('simpleshop/email/order/address-wrapper.php');
    ?>

    <br/>
    <br/>


    <?php if ($Payment): ?>
        <!-- payment -->
        <table <?= $styles['table'] ?>>
            <tr <?= $styles['tr'] ?>>
                <th>
                    <h3 <?= $styles['h2'] ?>>###label.payment_method###</h3>
                    <p <?= $styles['p'] ?>><?= $Payment->getName() ?><br/><?= $Payment->getValue('info') ?></p>
                </th>
                <th class="expander" style="<?= $styles['th'] ?>"></th>
            </tr>
        </table>
    <?php endif; ?>


    <?php if ($Shipping): ?>
        <!-- shipping -->
        <table <?= $styles['table'] ?>>
            <tr <?= $styles['tr'] ?>>
                <th>
                    <h3 <?= $styles['h2'] ?>>###label.shipping_method###</h3>
                    <p <?= $styles['p'] ?>><?= $Shipping->getName() ?></p>
                </th>
                <th class="expander" style="<?= $styles['th'] ?>"></th>
            </tr>
        </table>
    <?php endif; ?>

    <br/>
    <br/>

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

    $this->setVar('Order', $Order);
    $this->subfragment('simpleshop/email/order/conclusion.php');

    ?>
</div>
