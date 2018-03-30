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
$CNOrder = Order::get($Order->getValue('ref_order_id'));
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

?>
    <div>
        <br/>
        <strong>###company.name###</strong><br/>
        ###company.street###<br/>
        ###company.postal### ###company.location### (###company.province###)<br/>
        ###label.vat_short###: ###company.vat###<br/>
        ###company.shop_invoice_info###
    </div>
    <br/>

    <?php

$styles = array_merge([
    'css'   => '',
    'table' => '',
    'tr'    => '',
    'td'    => '',
    'total' => '',
], array_merge($config['email_tpl_styles'], [
    'table' => 'border-collapse:collapse;border-spacing:0;margin-top:20px;padding:0;text-align:left;vertical-align:top;width:100%;',
    'tr'    => 'border-bottom:1px solid #cacaca;padding:0;text-align:left;vertical-align:top;',
    'td'    => '-moz-hyphens:auto;-webkit-hyphens:auto;Margin:0;border-collapse:collapse!important;color:#0a0a0a;font-family:Helvetica,Arial,sans-serif;font-size:14px;font-weight:400;hyphens:auto;line-height:1.6;margin:0;padding:10px;vertical-align:top;word-wrap:break-word;',
    'total' => 'font-size:18px;font-weight:700;',
]));

?>
    <h2>
        <?= strtr(\Wildcard::get('shop.email.credit_note_subject'), [
            '{NUM}'  => $Order->getInvoiceNum(),
            '{DATE}' => date('d-m-Y', strtotime($Order->getValue('createdate'))),
        ]) ?>
    </h2>
    <!-- order conclusion/sum -->

    <table class="<?= $styles['css'] ?>" style="<?= $styles['table'] ?>">

        <!-- total -->
        <tr <?= $styles['tr'] ? 'style="' . $styles['tr'] . '"' : '' ?>>
            <td <?= $styles['td'] ? 'style="' . $styles['td'] . $styles['total'] . '"' : '' ?>>
                ###label.total_sum###
                <p style="font-weight:normal;font-size:14px;">
                    ###label.credit_note_reference_info### <?= $CNOrder->getInvoiceNum() ?>
                </p>
            </td>
            <td class="text-right" <?= $styles['td'] ? 'style="text-align:right;' . $styles['td'] . $styles['total'] . '"' : '' ?>>
                &euro; <?= format_price($Order->getValue('total', 0)) ?>
            </td>
        </tr>
    </table>

    <?php
$this->setVar('invoice_address', $Order->getInvoiceAddress());
$this->setVar('shipping_address', $Shipping ? $Order->getShippingAddress() : null);
$this->subfragment('simpleshop/email/order/address-wrapper.php');
?>