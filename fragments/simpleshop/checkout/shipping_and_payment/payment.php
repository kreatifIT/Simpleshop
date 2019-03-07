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


$Order    = $this->getVar('Order');
$payments = $this->getVar('payments');
$payment  = $Order->getValue('payment');

$this->setVar('payment', $payment ?: $payments[0]);


?>
<div class="payments margin-top margin-large-bottom">
    <!-- Payment -->
    <div class="row column">
        <h2 class="heading medium">###label.payment_method###</h2>
    </div>

    <div class="grid-x medium-up-<?= count($payments) ?> grid-margin-x grid-margin-y">
        <?php
        foreach ($payments as $index => $payment) {
            $this->setVar('self', $payment);

            echo '<div class="cell">';
            $this->subfragment("simpleshop/checkout/payment/{$payment->getPluginName()}/item.php");
            echo '</div>';
        }
        ?>
    </div>
</div>
