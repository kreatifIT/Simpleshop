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

$Order         = $this->getVar('Order');
$invoice_addr  = $Order->getInvoiceAddress();
$shipping_addr = $Order->getShippingAddress();

?>
<!-- Adressen -->
<div class="address-panels">
    <div class="row medium-up-2 ">
        <?php
        if ($shipping_addr) {
            $this->setVar('address', $shipping_addr);
            $this->setVar('title', '###shop.shipping_address###');
            $this->subfragment('simpleshop/checkout/summary/address_item.php');
        }
        if ($invoice_addr) {
            $this->setVar('address', $invoice_addr);
            $this->setVar('title', '###shop.invoice_address###');
            $this->subfragment('simpleshop/checkout/summary/address_item.php');
        }
        ?>
    </div>
</div>
