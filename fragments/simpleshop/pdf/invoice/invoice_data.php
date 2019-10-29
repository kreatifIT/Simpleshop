<?php

/**
 * This file is part of the Kreatif\Project package.
 *
 * @author Kreatif GmbH
 * @author a.platter@kreatif.it
 * Date: 30.05.18
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

$Order = $this->getVar('Order');
$type  = $this->getVar('type');

if ($type == 'invoice' && $Order->getValue('status') == 'CN') {
    $title = '###simpleshop.credit_note###: ' . $Order->getInvoiceNum();
}
else if ($type == 'invoice') {
    $title = '###simpleshop.invoice_num###: ' . $Order->getInvoiceNum();
}
else {
    $title = '###simpleshop.order_confirmation###: ' . $Order->getId();
}

?>
<table width="100%" id="invoice-data">
    <tr>
        <td width="70%" valign="bottom">
            <h1 id="invoice-data-title">
                <?= $title ?>
            </h1>
            <?php if ($type == 'invoice' && $Order->getValue('status') == 'CN'):
                $refOrderId = $Order->getValue('ref_order_id');

                if ($refOrderId) {
                    $CNOrder = \FriendsOfREDAXO\Simpleshop\Order::get($refOrderId);
                }
                ?>
                ###simpleshop.credit_note_reference_info### <?= $CNOrder ? $CNOrder->getInvoiceNum() : '-' ?>
            <?php endif; ?>
        </td>
        <td width="30%" valign="bottom" align="right">
            <div id="invoice-data-data">
                <?php if ($type == 'invoice' && $Order->getValue('status') != 'CN'): ?>
                    ###label.order###: <?= $Order->getId() ?><br/>
                <?php endif; ?>
                ###label.date###: <?= date('d/m/Y', strtotime($Order->getValue('createdate'))) ?>
            </div>
        </td>
    </tr>
</table>
