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

$title   = $this->getVar('title');
$url     = $this->getVar('url');
$address = $this->getVar('address');

?>
<div class="medium-6 columns margin-bottom">
    <div class="address-panel">
        <a href="<?= $url ?>" class="edit">
            <i class="fa fa-pencil hide-for-large" aria-hidden="true"></i>
            <span class="show-for-large">###action.edit###</span>
        </a>
        <h4><?= $title ?></h4>
        <p>
            <?= $address->getValue('firstname') ?> <?= $address->getValue('lastname') ?><?php if (strlen($address->getValue('additional'))) echo ' - '. $address->getValue('additional') ?><br/>
            <?= $address->getValue('street') ?><br>
            <?= $address->getValue('zip') ?> <?= $address->getValue('location') ?> - <br>
            <br>
            <?= $address->getValue('fiscal_code') ?>
        </p>
    </div>
</div>
