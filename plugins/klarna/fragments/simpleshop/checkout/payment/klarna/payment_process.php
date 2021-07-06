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


// set local for klarna
Utils::setCalcLocale();

$errors  = [];
$Order   = $this->getVar('Order');
$Payment = $Order->getValue('payment');

try {
    $Payment->processPayment($Order);
    rex_redirect(null, null, ['action' => 'complete', 'ts' => time()]);
} catch (\Throwable $ex) {
    $errors[] = 'Request Failed, pleas retry';
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
<?php endif; ?>