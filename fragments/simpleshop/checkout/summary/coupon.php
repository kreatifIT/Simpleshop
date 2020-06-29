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

$code = $this->getVar('coupon_code');

?>
<div class="checkout-coupon">
    <form action="<?= rex_getUrl(null, null, array_merge($_GET, ['ts' => time()])) ?>" method="post">
        <h3 class="heading small">###label.coupon###</h3>
        <div class="coupon-input-container">
            <input class="coupon-input" type="text" name="coupon_code" value="<?= $code ?>"/>
            <button type="submit" class="button coupon-submit" name="action" value="redeem_coupon">
                ###label.redeem_coupon###
            </button>
        </div>
    </form>
</div>
