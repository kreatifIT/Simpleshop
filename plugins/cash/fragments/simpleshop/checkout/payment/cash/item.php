<?php

/**
 * This file is part of the Kreatif\Project package.
 *
 * @author Kreatif GmbH
 * @author a.platter@kreatif.it
 * Date: 10.10.18
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace FriendsOfREDAXO\Simpleshop;

$self      = $this->getVar('self');
$payment   = $this->getVar('payment');
$is_active = is_object($payment) && $payment->getPluginName() == $self->getPluginName();

?>
<div class="checkout-radio-panel <?= $is_active ? 'selected' : '' ?>">
    <div class="custom-radio">
        <label>
            <?= $self->getName() ?>
            <input type="radio" name="payment" value="<?= $self->getPluginName() ?>" <?= $is_active ? 'checked="checked"' : '' ?>/>
            <span class="radio"></span>
        </label>
    </div>
</div>