<?php

/**
 * This file is part of the Kreatif\Project package.
 *
 * @author Kreatif GmbH
 * @author a.platter@kreatif.it
 * Date: 12.03.18
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

$payment = $this->getVar('payment');

?>
<div class="cell medium-6 margin-bottom">
    <h3 class="heading small">###label.payment_method###</h3>
    <?= $payment->getName() ?>
</div>
