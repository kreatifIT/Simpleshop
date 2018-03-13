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
$shipment  = $this->getVar('shipping');
$is_active = is_object($shipment) && $shipment->getPluginName() == $plugin;

?>
<div class="row column">
    <div class="custom-radio">
        <label>
            <?= $this->subfragment('simpleshop/shipping/' . $plugin . '/icon.svg') ?>
            <?= $name ?>
            <input type="radio" name="shipment" value="<?= $plugin ?>" <?= $is_active ? 'checked="checked"' : '' ?>/>
            <span class="radio"></span>
        </label>
    </div>
</div>