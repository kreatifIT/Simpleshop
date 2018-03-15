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

?>
<div class="row column margin-large-top margin-large-bottom">
    <h2 class="heading large separator">###label.cart###</h2>

    <div class="row">
        <div class="column cart-container">
            <?php
            $Controller = Simpleshop\CartController::execute();
            echo $Controller->parse();
            ?>
        </div>
    </div>
</div>
