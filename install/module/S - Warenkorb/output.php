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

use FriendsOfREDAXO\Simpleshop;


$values = rex_var::toArray('REX_VALUE[1]');

?>
<div class="row column margin-top margin-large-bottom">
    <?php if (strlen($values['title']) || strlen($values['text'])): ?>
        <div class="row column">
            <?php if (strlen($values['title'])): ?>
                <h2 class="heading large separator"><?= $values['title'] ?></h2>
            <?php endif; ?>
            <?= $values['text'] ?>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="column" data-cart-container>
            <?php
            $Controller = Simpleshop\CartController::execute();
            echo $Controller->parse();
            ?>
        </div>
    </div>
</div>