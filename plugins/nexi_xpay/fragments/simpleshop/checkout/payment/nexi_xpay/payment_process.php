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

// set local for nexi_xpay
Utils::setCalcLocale();

$errors = [];
$Order    = $this->getVar('Order');
$Payment  = $Order->getValue('payment');

try
{
    $Payment->processPayment($Order);
    rex_redirect(null, null, ['action' => 'complete', 'ts' => time()]);
}
catch (NexiXPayException $ex)
{
    switch ($ex->getCode())
    {
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
            $errors[] = strtr(Wildcard::get('error.ws_not_available'), ['{{service}}' => 'nexiXPay']);
            break;
        case 2:
            $errors[] = strtr(Wildcard::get('error.ws_not_reachable'), ['{{service}}' => 'nexiXPay']);
            break;
        case 3:
            $errors[] = strtr(Wildcard::get('error.ws_wrong_response_status'), ['{{service}}' => 'nexiXPay']);
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