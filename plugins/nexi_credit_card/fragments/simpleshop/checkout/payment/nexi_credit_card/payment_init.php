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

use Sprog\Wildcard;

Utils::setCalcLocale();

$errors   = [];
$Order    = $this->getVar('Order');
$Payment  = $Order->getValue('payment');
$order_id = $Order->getValue('id');

try {
    $url = $Payment->initPayment($order_id, $Order->getValue('total'), 'Order #' . $order_id);

    \rex_response::sendCacheControl();
    \rex_response::setStatus(\rex_response::HTTP_MOVED_TEMPORARILY);
    \rex_response::sendRedirect($url);
} catch (NexiException $ex) {
    switch ($ex->getCode()) {
        default:
            $errors[] = $ex->getMessage();
            break;
    }
} catch (WSConnectorException $ex) {
    switch ($ex->getCode()) {
        case 1:
            $errors[] = strtr(Wildcard::get('error.ws_not_available'), ['{{service}}' => 'Nexi']);
            break;
        case 2:
            $errors[] = strtr(Wildcard::get('error.ws_not_reachable'), ['{{service}}' => 'Nexi']);
            break;
        case 3:
            $errors[] = strtr(Wildcard::get('error.ws_wrong_response_status'), ['{{service}}' => 'Nexi']);
            break;
        default:
            $errors[] = $ex->getMessage();
            break;
    }
}

Utils::resetLocale();

if (count($errors)): ?>
    <div class="cell">
        <div class="margin-top margin-bottom">
            <?php foreach ($errors as $error): ?>
                <div class="callout alert"><?= $error ?></div>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>
