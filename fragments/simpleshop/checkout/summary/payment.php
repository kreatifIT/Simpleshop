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

$payment = $this->getVar('payment');

?>
<div class="medium-6 column margin-bottom">
    <h3>###label.payment_method###</h3>

    <?php
    $this->setVar('name', $payment->getName());
    $this->setVar('plugin_name', $payment->getPluginName());
    $this->subfragment('simpleshop/checkout/shipping_and_payment/payment_item.php');
    ?>
</div>
