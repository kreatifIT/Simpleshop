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


// set local for paypal
Utils::setCalcLocale();

$errors   = [];
$Order    = $this->getVar('Order');
$Payment  = $Order->getValue('payment');
$token    = rex_get('token', 'string');
$payer_id = rex_get('PayerID', 'string');

try {
    $Payment->processPayment($token, $payer_id, $Order);
    rex_redirect(null, null, ['action' => 'complete', 'ts' => time()]);
} catch (PaypalException $ex) {
    switch ($ex->getCode()) {
        case 11607:
            $errors[] = strtr(Wildcard::get('error.token_already_used_for_transaction'), ['{{service}}' => 'Paypal']);
            break;
        default:
            $errors[] = $ex->getMessage();
            break;
    }
} catch (WSConnectorException $ex) {
    switch ($ex->getCode()) {
        case 1:
            $errors[] = strtr(Wildcard::get('error.ws_not_available'), ['{{service}}' => 'Paypal']);
            break;
        case 2:
            $errors[] = strtr(Wildcard::get('error.ws_not_reachable'), ['{{service}}' => 'Paypal']);
            break;
        case 3:
            $errors[] = strtr(Wildcard::get('error.ws_wrong_response_status'), ['{{service}}' => 'Paypal']);
            break;
        default:
            $errors[] = $ex->getMessage();
            break;
    }
}

Utils::resetLocale();

if (count($errors)): ?>
    <div class="grid-container">
        <div class="cell">
            <div class="margin-top margin-bottom">
                <?php foreach ($errors as $error): ?>
                    <div class="callout alert"><?= $error ?></div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
<?php endif; ?>