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

$Order         = $this->getVar('Order');
$invoice_addr  = $Order->getInvoiceAddress();
$shipping_addr = $Order->getShippingAddress();


if (file_exists(\rex_path::base('resources/img/logo300dpi.png'))) {
    $logo = \rex_path::base('resources/img/logo300dpi.png');
} else {
    $logo = \rex_path::base('resources/img/email/logo.png');
}

?>
<div id="logo-row">
    <img src="<?= $logo ?>" height="50"/>
</div>

<table width="100%" id="invoice-header-infos">
    <tr>
        <td width="50%" valign="top">
            <?php
            if ($invoice_addr) {
                $customerData = $Order->getCustomerData();
                $invoice_addr->setValue('email', $customerData->getValue('email'));
                $this->setVar('address', $invoice_addr);
                $this->setVar('customer', $customerData);
                $this->setVar('title', '###label.invoice_address###');
                $this->subfragment('simpleshop/checkout/summary/address_item.php');
                echo '<br/><br/>';
            }
            if ($shipping_addr) {
                $this->setVar('address', $shipping_addr);
                $this->setVar('customer', null);
                $this->setVar('title', '###label.shipping_address###');
                $this->subfragment('simpleshop/checkout/summary/address_item.php');
            }
            ?>
        </td>
        <td width="50%" align="right" valign="top">
            <p>
                <?= $this->subfragment('simpleshop/general/company-data.php') ?>
            </p>
        </td>
    </tr>
</table>