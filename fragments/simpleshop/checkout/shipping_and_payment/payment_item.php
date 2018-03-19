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

$name      = $this->getVar('name');
$plugin    = $this->getVar('plugin_name');
$payment   = $this->getVar('payment');
$is_active = is_object($payment) && $payment->getPluginName() == $plugin;

?>
<div class="row column">
    <div class="checkout-radio-panel">
        <?= $this->subfragment('simpleshop/payment/' . $plugin . '/icon.svg') ?>
        <div class="custom-radio">
            <label>
                <?= $name ?>
                <input type="radio" name="payment" value="<?= $plugin ?>" <?= $is_active ? 'checked="checked"' : '' ?>/>
                <span class="radio"></span>
            </label>
        </div>
    </div>
</div>