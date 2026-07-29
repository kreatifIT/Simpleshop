<?php

/**
 * This file is part of the Simpleshop package.
 *
 * @author Kreatif GmbH
 * @author a.platter@kreatif.it
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FriendsOfREDAXO\Simpleshop;

// Composer-Autoloader laden, damit auch die `files`-Autoloads (z.B. GuzzleHttp-
// Funktionen wie choose_handler()) registriert werden. rex_autoload findet zwar
// die Klassen per Classmap, fuehrt aber composers Funktions-Autoload nicht aus.
if (is_readable(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
}

Shipping::register(DfiShipping::class, 'dfi_shipping');

\rex_extension::register('simpleshop.Order.completeOrder', [DfiOrderHandler::class, 'ext_completeOrder']);
