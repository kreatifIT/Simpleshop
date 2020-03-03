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

$title    = $this->getVar('title');
$address  = $this->getVar('address');
$Country  = $address->valueIsset('country') ? Country::get($address->getValue('country')) : null;

?>
<div class="cell margin-bottom">
    <div class="address">
        <?php if (strlen($title)): ?>
            <h4 class="heading small"><?= $title ?></h4>
        <?php endif; ?>
        <p>
            <?php
            $this->subfragment('simpleshop/customer/customer_area/address_data.php');
            ?>
        </p>
    </div>
</div>
