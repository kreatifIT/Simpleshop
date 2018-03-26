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

$step = rex_get('step', 'string');
pr($step);

?>
<div class="row column margin-top margin-large-bottom">
    <div class="checkout-steps">
        <div class="checkout-step-wrapper">
            <a href="<?= rex_getUrl(\Kreatif\Project\Settings::CART_PAGE_ID) ?>" class="checkout-step">
                <span class="checkout-step-number">1</span>
                <span class="checkout-step-name">###label.cart###</span>
            </a>
        </div>
        <div class="checkout-step-wrapper">
            <a href="<?= rex_getUrl(null, null, ['step' => 2]) ?>" class="checkout-step <?= $step == 2 ? 'active' : '' ?>">
                <span class="checkout-step-number">2</span>
                <span class="checkout-step-name">###label.invoice_address###</span>
            </a>
        </div>
        <div class="checkout-step-wrapper">
            <a <?php if ($step >= 3) {
                echo 'href="' . rex_getUrl(null, null, ['step' => 3]) . '"';
            } ?> class="checkout-step <?php if ($step == 3) {
                echo 'active';
            }
            elseif ($step < 3) {
                echo 'disabled';
            } ?>">
                <span class="checkout-step-number">3</span>
                <span class="checkout-step-name">###label.shipment_payment###</span>
            </a>
        </div>
        <div class="checkout-step-wrapper">
            <a <?php if ($step >= 4) {
                echo 'href="' . rex_getUrl(null, null, ['step' => 4]) . '"';
            } ?> class="checkout-step <?php if ($step == 4) {
                echo 'active';
            }
            elseif ($step < 4) {
                echo 'disabled';
            } ?>">
                <span class="checkout-step-number">4</span>
                <span class="checkout-step-name">###label.order###</span>
            </a>
        </div>
    </div>
</div>

