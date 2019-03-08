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

$self      = $this->getVar('self');
$shipping  = $this->getVar('shipping');
$is_active = is_object($shipping) && $shipping->getPluginName() == $self->getPluginName();

?>
<div class="checkout-radio-panel <?= $is_active ? 'selected' : '' ?>">
    <div class="custom-radio">
        <label>
            <?= $self->getName() ?>
            <input type="radio" name="shipment" value="<?= $self->getPluginName() ?>" <?= $is_active ? 'checked="checked"' : '' ?>/>
            <span class="radio"></span>
        </label>
    </div>
</div>