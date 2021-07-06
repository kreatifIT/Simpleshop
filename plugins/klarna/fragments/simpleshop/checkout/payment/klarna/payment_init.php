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

Utils::setCalcLocale();

$errors  = [];
$Order   = $this->getVar('Order');
$Payment = $Order->getValue('payment');

try {
    /** @var Klarna $Payment */
    $response = $Payment->initPayment($Order);
} catch (\Throwable $ex) {
    if (\rex_addon::get('project')->getProperty('compile') == 1) {
        $errors[] = $ex->getMessage();
    } else {
        $errors[] = 'Request Failed, pleas retry';
    }
    Utils::log('Klarna.initPayment', $ex->getMessage(), 'Error');
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
<?php else: ?>
    <div class="margin-top margin-bottom"><h1>###simpleshop.klarna_payment_title###</h1></div>
    <?= $response['html_snippet'] ?>
<?php endif; ?>
