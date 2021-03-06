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

\rex_extension::register('PACKAGES_INCLUDED', function ($params) {
    if (!\rex::isBackend()) {
        $action = rex_get('action', 'string');

        if ($action == 'process_ipn') {
            CheckoutController::processIPN();
        }
    }
});
\rex_extension::register('FE_OUTPUT', function ($params) {
    if (!\rex::isBackend()) {
        // api endpoint
        $api_result = \rex_api_simpleshop_api::factory();
        if ($api_result && $api_result->hasMessage()) {
            header('Content-Type: application/json');
            echo $api_result->getResult()
                ->toJSON();
            exit;
        } else {
            // save url to session
            $session = Session::getSession();
            $session->writeSession([
                'last_url' => rex_getUrl(),
            ]);
        }
    }
    return $params->getSubject();
});