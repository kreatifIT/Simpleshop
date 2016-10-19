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

$name     = $this->getVar('name');
$plugin   = $this->getVar('plugin_name');
$shipment = $this->getVar('shipment');
$is_active = is_object($shipment) && $shipment->getPluginName() == $plugin;

?>
<div class="medium-6 columns margin-bottom">
    <div class="radio-panel <?php if ($is_active) echo 'selected'; ?>">
        <div>
            <?= $this->subfragment('simpleshop/shipping/' . $plugin . '/icon.svg') ?>
        </div>
        <div class="custom-radio">
            <label>
                <?= $name ?>
                    <input type="radio" name="shipment" value="<?= $plugin ?>" <?php if ($is_active) echo 'checked="checked"'; ?> required/>
                <span class="radio"></span>
            </label>
        </div>
    </div>
</div>