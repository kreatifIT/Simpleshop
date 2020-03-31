<?php

/**
 * This file is part of the Kreatif\Project package.
 *
 * @author Kreatif GmbH
 * @author a.platter@kreatif.it
 * Date: 31/03/2020
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

$payment = $this->getVar('payment');

?>
<div class="shop-redirect-wrapper padding-top padding-bottom">
    <?= str_replace('{NAME}', "<strong>{$payment->getName()}</strong>", Wildcard::get('label.redirect_to_plugin_payment_page')) ?>
</div>