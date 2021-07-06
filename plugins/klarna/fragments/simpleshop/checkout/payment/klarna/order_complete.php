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

$order     = $this->getVar('Order');
$color     = $this->getVar('primary_color');
$payment   = $order->getValue('payment');
$responses = $payment->getValue('responses');

?>
<div class="margin-bottom">
    <?php if ($color == ''): ?>
        <?= $responses['pay-process']['html_snippet'] ?>
    <?php endif; ?>
</div>
