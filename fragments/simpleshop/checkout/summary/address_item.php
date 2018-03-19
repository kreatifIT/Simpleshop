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
        <h4 class="heading small"><?= $title ?></h4>
        <p>
            <?php

            if ($address->getName()) {
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
            <?= $address->getValue('location') ?> - <?= $address->getValue('province') ?><br>
        </p>
    </div>
</div>
