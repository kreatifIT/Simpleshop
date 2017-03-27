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
setlocale(LC_ALL, 'en_US');

$errors = [];
$Order    = $this->getVar('Order');
$Payment  = $Order->getValue('payment');
$token    = rex_get('token', 'string');
$payer_id = rex_get('PayerID', 'string');

try
{
    $Payment->processPayment($token, $payer_id, $Order->getValue('total'));
    rex_redirect(null, null, ['action' => 'complete']);
}
catch (PaypalException $ex)
{
    switch ($ex->getCode())
    {
        case 11607:
            $errors[] = checkstr(strtr(Wildcard::get('error.token_already_used_for_transaction'), ['{{service}}' => 'Paypal']), $ex->getMessage());
            break;
        default:
            $errors[] = $ex->getMessage();
            break;
    }
}
catch (WSConnectorException $ex)
{
    switch ($ex->getCode())
    {
        case 1:
            $errors[] = checkstr(strtr(Wildcard::get('error.ws_not_available'), ['{{service}}' => 'Paypal']), $ex->getMessage());
            break;
        case 2:
            $errors[] = checkstr(strtr(Wildcard::get('error.ws_not_reachable'), ['{{service}}' => 'Paypal']), $ex->getMessage());
            break;
        case 3:
            $errors[] = checkstr(strtr(Wildcard::get('error.ws_wrong_response_status'), ['{{service}}' => 'Paypal']), $ex->getMessage());
            break;
        default:
            $errors[] = $ex->getMessage();
            break;
    }
}

if (count($errors)): ?>
    <div class="row column">
        <div class="margin-top margin-bottom">
            <?php foreach ($errors as $error): ?>
                <div class="callout alert"><?= $error ?></div>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>