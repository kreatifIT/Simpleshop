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

$step = $this->getVar('current_step');

?>
<div class="steps-checkout row clearfix margin-top margin-bottom">
    <div class="step-container">
        <a href="<?= rex_getUrl(\Kreatif\Project\Settings::CART_PAGE_ID) ?>" class="step clearfix">
            <span class="step-number">1</span>
            <span class="step-name">###label.cart###</span>
        </a>
    </div>
    <div class="step-container">
        <a href="<?= rex_getUrl(null, null, ['step' => 2]) ?>" class="step clearfix  <?php if ($step == 2) echo 'active';  ?>">
            <span class="step-number">2</span>
            <span class="step-name">###label.address###</span>
        </a>
    </div>
    <div class="step-container">
        <a <?php if ($step >= 3) echo 'href="'. rex_getUrl(null, null, ['step' => 3]) .'"'; ?> class="step clearfix <?php if ($step == 3) echo 'active'; elseif ($step < 3) echo 'disabled';  ?>">
            <span class="step-number">3</span>
            <span class="step-name">###label.shipment_payment###</span>
        </a>
    </div>
    <div class="step-container">
        <a <?php if ($step >= 4) echo 'href="'. rex_getUrl(null, null, ['step' => 4]) .'"'; ?> class="step clearfix <?php if ($step == 4) echo 'active'; elseif ($step < 4) echo 'disabled';  ?>">
            <span class="step-number">4</span>
            <span class="step-name">###label.order###</span>
        </a>
    </div>
</div>