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

?>
<div id="shop-modal" class="shop-modal">
    <div class="row shop-modal-content">
        
    </div>
    <span class="row column horizontal-rule double-rule"></span>
    <div class="row buttons-checkout">
        <div class="medium-6 columns">
            <button class="button button-gray button-close">###action.continue_shopping###</button>
        </div>
        <div class="medium-6 columns">
            <a href="<?= rex_getUrl(\Kreatif\Project\Settings::CART_PAGE_ID) ?>" class="button button-checkout">###action.go_to_cart###</a>
        </div>
    </div>
</div>