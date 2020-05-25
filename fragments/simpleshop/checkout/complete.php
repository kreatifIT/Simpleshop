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

$Order    = $this->getVar('Order');
$Payment  = $Order->getValue('payment');
$Shipping = $Order->getValue('shipping');

?>
<div class="order-complete margin-large-top margin-large-bottom">
    <h2 class="text-center margin-bottom">###label.order_placed###</h2>
    <?php
    if ($Order->getValue('total') > 0 && $Order->getValue('payment')) {
        $this->subfragment('simpleshop/checkout/payment/' . $Payment->plugin_name . '/order_complete.php');
    }
    if ($Shipping) {
        $this->subfragment('simpleshop/checkout/shipping/' . $Shipping->plugin_name . '/order_complete.php');
    }
    ?>
</div>