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

$Order     = $this->getVar('Order');
$payment   = $Order->getValue('payment');


try {
    /** @var Klarna $payment */
    $payment->placeOrder($Order);
    rex_redirect(null, null, ['action' => 'complete', 'ts' => time()]);
} catch (\Throwable $ex) {
    switch ($ex->getCode()) {
        default:
            $errors[] = $ex->getMessage();
            break;
    }
    Utils::log('Klarna.placeOrder', $ex->getMessage(), 'Error');
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
