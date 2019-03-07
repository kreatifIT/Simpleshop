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
<div class="coupon margin-bottom">
    <form action="" method="post">
        <h3>###label.coupon###</h3>
        <input type="text" name="coupon" value="<?= $code ?>"/>
        <button type="submit" class="button coupon-btn" name="action" value="redeem_coupon">
            ###simpleshop.redeem_coupon###
        </button>
    </form>
</div>