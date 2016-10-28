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
<div class="large-4 columns margin-bottom box-checkout">
    <div>
        <h3>###action.shop_as_guest###</h3>
        <p>###shop.buy_as_guest_text###</p>
        <a href="<?= rex_getUrl(NULL, NULL, ['action' => 'guest-checkout']) ?>" class="button expanded">###action.continue_as_guest###</a>
    </div>
</div>
