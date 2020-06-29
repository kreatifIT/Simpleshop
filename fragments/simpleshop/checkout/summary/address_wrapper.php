<?php

/**
 * This file is part of the Kreatif\Project package.
 *
 * @author Kreatif GmbH
 * @author a.platter@kreatif.it
 * Date: 12.03.18
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

$Order        = $this->getVar('Order');
$invoiceAddr  = $Order->getInvoiceAddress();
$shippingAddr = $Order->getShippingAddress();

$invoiceAddrData  = $invoiceAddr ? $invoiceAddr->toAddressArray(true) : [];
$shippingAddrData = $shippingAddr ? $shippingAddr->toAddressArray(false) : [];

?>
<!-- Adressen -->
<div class="address-panels">
    <div class="grid-x grid-margin-x medium-up-2">
        <div class="cell margin-bottom">
            <div class="address">
                <h4 class="heading small">###label.invoice_address###</h4>
                <p>
                    <?= implode('<br/>', $invoiceAddrData) ?>
                </p>
            </div>
        </div>

        <div class="cell margin-bottom">
            <div class="address">
                <h4 class="heading small">###label.shipping_address###</h4>
                <p>
                    <?= implode('<br/>', $shippingAddrData) ?>
                </p>
            </div>
        </div>

    </div>
</div>
