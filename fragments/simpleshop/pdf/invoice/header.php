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

$Order        = $this->getVar('Order');
$invoiceAddr  = $Order->getInvoiceAddress();
$shippingAddr = $Order->getShippingAddress();

$invoiceAddrData  = $invoiceAddr ? $invoiceAddr->toAddressArray(true) : [];
$shippingAddrData = $shippingAddr ? $shippingAddr->toAddressArray(false) : [];


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

            <div class="address">
                <h4 class="heading small">###label.invoice_address###</h4>
                <p>
                    <?= implode('<br/>', $invoiceAddrData) ?>
                </p>
            </div>

            <br/>
            <br/>

            <div class="address">
                <h4 class="heading small">###label.shipping_address###</h4>
                <p>
                    <?= implode('<br/>', $shippingAddrData) ?>
                </p>
            </div>

        </td>
        <td width="50%" align="right" valign="top">
            <p>
                <?= $this->subfragment('simpleshop/general/company-data.php') ?>
            </p>
        </td>
    </tr>
</table>