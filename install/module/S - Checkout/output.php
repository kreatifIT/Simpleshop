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

if (rex::isBackend()) {
    echo '
        <div class="modul-content">
            <div class="form-horizontal">
                <h1 style="margin:40px 0;text-align:center;">Checkout-Prozess</h1>
            </div>
        </div>
    ';
    return;
}

$Controller = Simpleshop\CheckoutController::execute([
    'action' => rex_get('action', 'string', 'address'),
]);
echo $Controller->parse();