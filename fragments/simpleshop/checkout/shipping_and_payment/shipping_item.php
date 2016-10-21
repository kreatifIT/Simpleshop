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

$class    = $this->getVar('class');
$before   = $this->getVar('before');
$after    = $this->getVar('after');
$name     = $this->getVar('name');
$plugin   = $this->getVar('plugin_name');
$shipment = $this->getVar('shipping');
$is_active = is_object($shipment) && $shipment->getPluginName() == $plugin;

?>
<div class="<?= $class ?>">
    <div class="radio-panel <?php if ($is_active) echo 'selected'; ?>">
        <?= $before ?>
        <div>
            <?= $this->subfragment('simpleshop/shipping/' . $plugin . '/icon.svg') ?>
        </div>
        <div class="custom-radio">
            <label>
                <?= $name ?>
                <input type="radio" name="shipment" value="<?= $plugin ?>" <?php if ($is_active) echo 'checked="checked"'; ?>/>
                <span class="radio"></span>
            </label>
        </div>
        <?= $after ?>
    </div>
</div>