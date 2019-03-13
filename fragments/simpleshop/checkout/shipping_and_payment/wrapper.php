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

$currentStep = $this->getVar('current_step');
$back_url    = $this->getVar('back_url');

?>
<div class="shipping-payment">
    <form action="<?= rex_getUrl(null, null, array_merge($_GET, ['ts' => time()])) ?>" method="post">
        <?php
        if ($currentStep == 'shipping||payment' || $currentStep == 'shipping') {
            $this->subfragment('simpleshop/checkout/shipping_and_payment/shipping.php');
        }

        if ($currentStep == 'shipping||payment' || $currentStep == 'payment') {
            $this->subfragment('simpleshop/checkout/shipping_and_payment/payment.php');
        }
        ?>
        <div class="margin-large-bottom">
            <a href="<?= $back_url ?>" class="button margin-bottom">###action.go_back###</a>
            <button type="submit" class="button margin-bottom secondary float-right" name="action" value="set-shipping-payment">###action.go_ahead###</button>
        </div>
    </form>
</div>
