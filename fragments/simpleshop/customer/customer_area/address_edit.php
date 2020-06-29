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

$address_id = rex_get('data-id', 'int');
$Address    = $address_id ? CustomerAddress::get($address_id) : CustomerAddress::create();
$title      = $Address ? '###label.edit_address###' : '###label.new_address###';

?>
<div class="customer-area-address">
    <h2 class="margin-small-bottom">
        <?= $title ?>
    </h2>
    <div class="navigation margin-bottom">
        <a href="<?= rex_getUrl(null, null, ['ctrl' => 'addresses']) ?>">&laquo; ###action.back_to_overview###</a>
        <hr>
    </div>

    <?php
    $fragment = new \rex_fragment();
    $fragment->setVar('Address', $Address);
    echo $fragment->parse('simpleshop/customer/customer_area/shipping_address.php');
    ?>
</div>
