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
$Customer = $this->getVar('customer');
$Country  = $address->valueIsset('country') ? Country::get($address->getValue('country')) : null;

?>
<div class="cell margin-bottom">
    <div class="address">
        <?php if (strlen($title)): ?>
        <h4 class="heading small"><?= $title ?></h4>
        <?php endif; ?>
        <p>
            <?php
            if ($Customer) {
                echo $Customer->getName() . '<br>';
            }
            else {
                echo $address->getName() . '<br>';
            }

            if ($address->valueIsset('street')) {
                echo $address->getValue('street') . '<br>';
            }
            if ($address->valueIsset('street_additional')) {
                echo $address->getValue('street_additional') . '<br>';
            }
            ?>
            <?= $address->getValue('postal') ?>
            <?= $address->getValue('location') ?> <?= $address->valueIsset('province') ? '- ' . $address->getValue('province') : '' ?><br>
            <?= $Country ? $Country->getName() : '' ?><br>

            <?php
            if ($Customer) {
                if ($Customer->valueIsset('fiscal_code')) {
                    echo $Customer->getValue('fiscal_code') .'<br/>';
                }
                if ($Customer->getValue('ctype') == 'company') {
                    echo '###label.vat_short###: '. $Customer->getValue('vat_num') .'<br/>';
                }
            }
            ?>
        </p>
    </div>
</div>
