<?php

/**
 * This file is part of the Kreatif\Project package.
 *
 * @author Kreatif GmbH
 * @author a.platter@kreatif.it
 * Date: 08.08.18
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace FriendsOfREDAXO\Simpleshop;

$User  = $this->getVar('User');
$title = $this->getVar('title', '###label.account_data###');
$text  = $this->getVar('text');

?>
<div class="customer-area-address">
    <h2 class="<?= strlen($text) ? 'margin-bottom' : '' ?>"><?= $title ?></h2>
    <?= strlen($text) ? $text : '' ?>

    <?php
    $fragment = new \rex_fragment();
    echo $fragment->parse('simpleshop/customer/customer_area/account_form.php');
    ?>
</div>
