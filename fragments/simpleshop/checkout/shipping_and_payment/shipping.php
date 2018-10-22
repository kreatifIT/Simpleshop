<?php

/**
 * This file is part of the Kreatif\Project package.
 *
 * @author Kreatif GmbH
 * @author a.platter@kreatif.it
 * Date: 10.10.18
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace FriendsOfREDAXO\Simpleshop;


$Order     = $this->getVar('Order');
$shippings = $this->getVar('shippings');
$shipping  = $Order->getValue('shipping');

$this->setVar('shipping', $shipping ?: $shippings[0]);


?>
<div class="shippings margin-top margin-large-bottom">
    <!-- Shipment -->
    <div class="row column">
        <h2 class="heading medium">###label.shipping_method###</h2>
    </div>

    <div class="row medium-up-2">
        <?php
        foreach ($shippings as $index => $shipping) {
            $this->setVar('self', $shipping);
            $this->subfragment("simpleshop/checkout/shipping/{$shipping->getPluginName()}/item.php");
        }
        ?>
    </div>
</div>
