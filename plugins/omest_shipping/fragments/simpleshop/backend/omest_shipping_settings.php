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

    <legend><?= $Addon->i18n('omest_shipping.basic_settings'); ?></legend>
    <dl class="rex-form-group form-group">
        <dt><?= $Addon->i18n('omest_shipping.warehouse_key'); ?></dt>
        <dd>
            <input type="text" class="form-control" name="warehouse_key" value="<?= from_array($Settings, 'warehouse_key') ?>"/>
        </dd>
    </dl>
    <dl class="rex-form-group form-group">
        <dt><?= $Addon->i18n('omest_shipping.omest_pickup'); ?></dt>
        <dd>
            <select class="form-control">
                <option value="0"><?= $Addon->i18n('no') ?></option>
                <option value="1"><?= $Addon->i18n('yes') ?></option>
            </select>
        </dd>
    </dl>
    <dl class="rex-form-group form-group">
        <dt><?= $Addon->i18n('omest_shipping.pickup_zip'); ?></dt>
        <dd>
            <input type="text" class="form-control" name="pickup_zip" value="<?= from_array($Settings, 'pickup_zip') ?>"/>
        </dd>
    </dl>
    <dl class="rex-form-group form-group">
        <dt><?= $Addon->i18n('omest_shipping.pickup_country_code'); ?></dt>
        <dd>
            <input type="text" class="form-control" name="pickup_country_code" value="<?= from_array($Settings, 'pickup_country_code') ?>"/>
        </dd>
    </dl>


    <legend><?= $Addon->i18n('omest_shipping.api_settings'); ?></legend>
    <dl class="rex-form-group form-group">
        <dt>Test-Modus:</dt>
        <dd>
            <label class="form-label">
                <input type="checkbox" class="form-control" name="sandbox" value="1" <?= from_array($Settings, 'sandbox') ? 'checked="checked"' : '' ?>/>
                <span></span>
                <?= $Addon->i18n('label.activated'); ?>
            </label>
        </dd>
    </dl>
    <dl class="rex-form-group form-group">
        <dt>Api-Key:</dt>
        <dd>
            <input type="text" class="form-control" name="api_key" value="<?= from_array($Settings, 'api_key') ?>"/>
        </dd>
    </dl>
    <dl class="rex-form-group form-group">
        <dt>Customer-Key:</dt>
        <dd>
            <input type="text" class="form-control" name="customer_key" value="<?= from_array($Settings, 'customer_key') ?>"/>
        </dd>
    </dl>
    <dl class="rex-form-group form-group">
        <dt>TEST Api-Key:</dt>
        <dd>
            <input type="text" class="form-control" name="test_api_key" value="<?= from_array($Settings, 'test_api_key') ?>"/>
        </dd>
    </dl>
    <dl class="rex-form-group form-group">
        <dt>TEST Customer-Key:</dt>
        <dd>
            <input type="text" class="form-control" name="test_customer_key" value="<?= from_array($Settings, 'test_customer_key') ?>"/>
        </dd>
    </dl>

</fieldset>