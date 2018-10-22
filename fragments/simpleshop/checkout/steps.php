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

$current_step = $this->getVar('current_step');
$step_number  = array_search($current_step, FragmentConfig::$data['checkout']['steps']);

switch ($current_step) {
    case 'invoice_address' :
        $step_number = 1;
        break;
    case 'shipping_address' :
        $step_number = 2;
        break;
    case 'shipping' :
    case 'payment' :
    case 'shipping||payment' :
        $step_number = 3;
        break;
    case 'show-summary' :
        $step_number = 4;
        break;
}

?>
<div class="row column margin-top margin-large-bottom">
    <div class="checkout-steps">
        <div class="checkout-step-wrapper">
            <a href="<?= rex_getUrl(null, null, ['step' => 'invoice_address']) ?>" class="checkout-step <?= $step_number == 1 ? 'active' : '' ?>">
                <span class="checkout-step-number">1</span>
                <span class="checkout-step-name">###label.invoice_address###</span>
            </a>
        </div>
        <div class="checkout-step-wrapper">
            <a <?php if ($step_number >= 2) {
                echo 'href="' . rex_getUrl(null, null, ['step' => 'shipping_address']) . '"';
            } ?> class="checkout-step <?php if ($step_number == 2) {
                echo 'active';
            } else if ($step_number < 2) {
                echo 'disabled';
            } ?>">
                <span class="checkout-step-number">2</span>
                <span class="checkout-step-name">###label.shipping_address###</span>
            </a>
        </div>
        <div class="checkout-step-wrapper">

            <a <?php if ($step_number >= 3) {
                echo 'href="' . rex_getUrl(null, null, ['step' => FragmentConfig::$data['checkout']['steps'][2]]) . '"';
            } ?> class="checkout-step <?php if ($step_number == 3) {
                echo 'active';
            } else if ($step_number < 3) {
                echo 'disabled';
            } ?>">
                <span class="checkout-step-number">3</span>
                <span class="checkout-step-name">###label.shipment_payment###</span>
            </a>
        </div>
        <div class="checkout-step-wrapper">
            <a <?php if ($step_number == 4) {
                echo 'href="' . rex_getUrl(null, null, ['step' => 'show-summary']) . '"';
            } ?> class="checkout-step <?php if ($step_number == 4) {
                echo 'active';
            } else if ($step_number < 4) {
                echo 'disabled';
            } ?>">
                <span class="checkout-step-number">4</span>
                <span class="checkout-step-name">###label.order###</span>
            </a>
        </div>
    </div>
</div>

