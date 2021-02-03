<?php

/**
 * This file is part of the Kreatif\Project package.
 *
 * @author Kreatif GmbH
 * @author a.platter@kreatif.it
 * Date: 15/06/2020
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

\rex_extension::register('simpleshop.orderFunctionsOutput', ['\FriendsOfREDAXO\Simpleshop\Ombis\Order', 'ext__orderFunctionsOutput']);
\rex_extension::register('simpleshop.Order.completeOrder', ['\FriendsOfREDAXO\Simpleshop\Ombis\Order', 'ext__createPreVKDokument']);

if (rex::isBackend() && rex::getUser()) {
    $currentAddon = current(explode('/', rex_be_controller::getCurrentPage()));
    if ($currentAddon == 'simpleshop' && \FriendsOfREDAXO\Simpleshop\Ombis\Api::testConnection() != 'OK') {
        $errors   = \rex::getProperty('simpleshop.backend_errors', []);
        $errors[] = ['label' => 'Ombis API Connection test failed!'];
        \rex::setProperty('simpleshop.backend_errors', $errors);
    }
}