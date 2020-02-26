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

$step_number      = 0;
$current_step     = $this->getVar('current_step');
$current_step_num = array_search($current_step, FragmentConfig::$data['checkout']['steps']) + 1;
$stepConfig       = FragmentConfig::getValue('checkout.steps');

?>
<div class="margin-top margin-bottom">
    <div class="checkout-steps">

        <?php if (in_array('invoice_address', $stepConfig)): ?>
            <?php $step_number++ ?>
            <div class="checkout-step-wrapper">
                <a href="<?= rex_getUrl(null, null, ['step' => 'invoice_address', 'ts' => time()]) ?>" class="checkout-step <?= $step_number == $current_step ? 'active' : '' ?>">
                    <span class="checkout-step-number"><?= $step_number ?></span>
                    <span class="checkout-step-name">###label.invoice_address###</span>
                </a>
            </div>
        <?php endif; ?>

        <?php if (in_array('shipping_address', $stepConfig)): ?>
            <?php $step_number++ ?>
            <div class="checkout-step-wrapper">
                <a <?php if ($current_step_num >= $step_number) {
                    echo 'href="' . rex_getUrl(null, null, ['step' => 'shipping_address', 'ts' => time()]) . '"';
                } ?> class="checkout-step <?php if ($current_step_num == $step_number) {
                    echo 'active';
                } else if ($current_step_num < $step_number) {
                    echo 'disabled';
                } ?>">
                    <span class="checkout-step-number"><?= $step_number ?></span>
                    <span class="checkout-step-name">###label.shipping_address###</span>
                </a>
            </div>
        <?php endif; ?>

        <?php if (in_array('payment', $stepConfig) || in_array('shipping', $stepConfig) || in_array('shipping||payment', $stepConfig)): ?>
            <?php $step_number++; ?>
            <div class="checkout-step-wrapper">
                <a <?php if ($current_step_num >= $step_number) {
                    echo 'href="' . rex_getUrl(null, null, ['step' => FragmentConfig::$data['checkout']['steps'][2], 'ts' => time()]) . '"';
                } ?> class="checkout-step <?php if ($current_step_num == $step_number) {
                    echo 'active';
                } else if ($current_step_num < $step_number) {
                    echo 'disabled';
                } ?>">
                    <span class="checkout-step-number"><?= $step_number ?></span>
                    <span class="checkout-step-name">###label.shipment_payment###</span>
                </a>
            </div>
        <?php endif; ?>

        <?php if (in_array('show-summary', $stepConfig)): ?>
            <?php $step_number++ ?>
            <div class="checkout-step-wrapper">
                <a <?php if ($current_step_num == $step_number) {
                    echo 'href="' . rex_getUrl(null, null, ['step' => 'show-summary', 'ts' => time()]) . '"';
                } ?> class="checkout-step <?php if ($current_step_num == $step_number) {
                    echo 'active';
                } else if ($current_step_num < $step_number) {
                    echo 'disabled';
                } ?>">
                    <span class="checkout-step-number"><?= $step_number ?></span>
                    <span class="checkout-step-name">###label.order###</span>
                </a>
            </div>
        <?php endif; ?>

    </div>
</div>

