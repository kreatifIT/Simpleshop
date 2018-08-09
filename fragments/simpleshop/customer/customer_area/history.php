<?php

/**
 * This file is part of the Kreatif\Project package.
 *
 * @author Kreatif GmbH
 * @author a.platter@kreatif.it
 * Date: 07.08.18
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace FriendsOfREDAXO\Simpleshop;


$User  = $this->getVar('User');
$title = $this->getVar('title', '###label.account_data###');
$text  = $this->getVar('text');

$action = rex_get('action', 'string');

?>
<div class="member-area--history">
    <div class="row column">
        <h2 class="<?= strlen($text) ? 'margin-bottom' : '' ?>"><?= $title ?></h2>
        <?= strlen($text) ? $text : '' ?>
    </div>

    <?php $this->subfragment('simpleshop/customer/customer_area/order/history_list.php') ?>
</div>