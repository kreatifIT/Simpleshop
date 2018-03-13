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
$address = $this->getVar('address');

?>
<div class="column margin-bottom">
    <div class="address">
        <h4><?= $title ?></h4>
        <p>
            <?= $address->getName() ?>
            <br/>
            <?= $address->getValue('street') ?><br>
            <?php
            if ($address->valueIsset('street_additional')) {
                echo $address->getValue('street_additional') .'<br/>';
            }
            ?>
            <?= $address->getValue('postal') ?>
            <?= $address->getValue('location') ?> - <?= $address->getValue('province') ?><br>
        </p>
    </div>
</div>
