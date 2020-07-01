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


$Addon    = $this->getVar('Addon');
$Settings = $this->getVar('Settings');

?>
<fieldset>

    <legend><?= $Addon->i18n('settings.shop_settings'); ?></legend>
    <dl class="rex-form-group form-group">
        <dt><?= $Addon->i18n('settings.order_notification_email'); ?></dt>
        <dd><input type="text" class="form-control" name="order_notification_email" value="<?= from_array($Settings, 'order_notification_email') ?>"/></dd>
    </dl>

    <br/>
    <br/>

    <dl class="rex-form-group form-group">
        <dt><?= $Addon->i18n('settings.min_order_value'); ?></dt>
        <dd><input type="text" class="form-control" name="min_order_value" value="<?= from_array($Settings, 'min_order_value') ?>"/></dd>
    </dl>

    <br/>
    <br/>

    <?php if (FragmentConfig::getValue('cart.use_discount_groups')): ?>
        <legend><?= $Addon->i18n('settings.discount_settings'); ?></legend>
        <dl class="rex-form-group form-group">
            <dt><?= $Addon->i18n('settings.discount_application'); ?></dt>
            <dd>
                <label class="form-label">
                    <input type="checkbox" name="discounts_are_accumulable" value="1" <?= from_array($Settings, 'discounts_are_accumulable') == 1 ? 'checked="checked"' : '' ?>/>
                    <span></span>
                    <?= $Addon->i18n('settings.discounts_are_accumulable'); ?>
                </label>
            </dd>
        </dl>
        <br/>
        <br/>
    <?php endif; ?>

</fieldset>