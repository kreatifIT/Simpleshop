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

$Order           = $this->getVar('Order');
$Payment         = $Order->getValue('payment');
$Shipping        = $Order->getValue('shipping');
$additional_info = \rex_extension::registerPoint(new \rex_extension_point('simpleshop.checkout.complete_additional_info', ''));

?>
<div class="order-complete margin-large-top margin-large-bottom">
    <div class="row column">
        <h2 class="text-center margin-bottom">###simpleshop.order_placed###</h2>
    </div>
    <?php
    if ($Order->getValue('total') > 0 && $Order->getValue('payment')) {
        $this->subfragment('simpleshop/payment/' . $Payment->plugin_name . '/order_complete.php');
    }
    if ($Shipping) {
        $this->subfragment('simpleshop/shipping/' . $Shipping->plugin_name . '/order_complete.php');
    }
    ?>

    <?php if (strlen($additional_info)): ?>
        <div class="row column text-center margin-bottom">
            <p><?= $additional_info ?></p>
        </div>
    <?php endif; ?>
</div>


