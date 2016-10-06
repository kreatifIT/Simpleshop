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

$i = $this->getVar('button-cart-counter');
?>
<div>
    <?php if ($this->getVar('has_quantity_control')): ?>
        <div class="amount-increment clearfix">
            <button class="button minus">-</button>
            <input type="text" value="1" readonly>
            <button class="button plus">+</button>
        </div>
    <?php endif; ?>
    <a class="add-to-cart fbox" href="#proceed-to-cart-<?= $i ?>">
        <i class="fa fa-cart-plus" aria-hidden="true"></i>
        <span><?= $this->i18n('action.add_to_cart'); ?></span>
    </a>
</div>