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

$code = $this->getVar('code');

?>
<div class="coupon row column margin-bottom">

    <form action="" method="post">
        <h3>###shop.coupon###</h3>
        <p>###shop.apply_coupon_text###</p>
        <input type="text" name="coupon" value="<?= $code ?>"/>
        <button type="submit" class="button" name="action" value="redeem_coupon">###shop.redeem_coupon###</button>
    </form>
</div>