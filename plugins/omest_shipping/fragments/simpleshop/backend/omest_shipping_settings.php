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


$Addon        = $this->getVar('Addon');
$Settings     = $this->getVar('Settings');
$serviceTypes = (array)from_array($Settings, 'service_types', []);

?>
<fieldset>

    <legend>Konfiguration</legend>
    <dl class="rex-form-group form-group">
        <?php
        $frontend_use = from_array($Settings, 'used_in_frontend', 1);
        ?>
        <dt>Als Option im Frontend anzeigen:</dt>
        <dd>
            <select class="form-control" name="used_in_frontend">
                <option value="1"><?= $Addon->i18n('yes'); ?></option>
                <option value="0" <?= !$frontend_use ? 'selected="selected"' : '' ?>><?= $Addon->i18n('no'); ?></option>
            </select>
        </dd>
    </dl>
    <dl class="rex-form-group form-group">
        <dt>Versandarten</dt>
        <dd>
            <select class="form-control" name="service_types[]" multiple>
                <option value="EC" <?= in_array('EC', $serviceTypes) ? 'selected="selected"' : '' ?>>Economy</option>
                <option value="EX" <?= in_array('EX', $serviceTypes) ? 'selected="selected"' : '' ?>>Express</option>
            </select>
        </dd>
    </dl>
    <br/>
    <br/>

    <legend><?= $Addon->i18n('omest_shipping.basic_settings'); ?></legend>
    <dl class="rex-form-group form-group">
        <dt><?= $Addon->i18n('omest_shipping.warehouse_key'); ?></dt>
        <dd>
            <input type="text" class="form-control" name="warehouse_key" value="<?= from_array($Settings, 'warehouse_key', 'OMEST S.A.S') ?>" placeholder='"WH1" oder "OMEST S.A.S" für bei Omest lagernde Ware'/>
        </dd>
    </dl>
    <dl class="rex-form-group form-group">
        <dt><?= $Addon->i18n('omest_shipping.omest_pickup'); ?></dt>
        <dd>
            <select class="form-control" name="omest_pickup">
                <option value="0"><?= $Addon->i18n('no') ?></option>
                <option value="1" <?= from_array($Settings, 'omest_pickup') ? 'selected="selected"' : '' ?>><?= $Addon->i18n('yes') ?></option>
            </select>
        </dd>
    </dl>
    <dl class="rex-form-group form-group">
        <dt><?= $Addon->i18n('omest_shipping.pickup_company_name'); ?></dt>
        <dd>
            <input type="text" class="form-control" name="pickup_company_name" value="<?= from_array($Settings, 'pickup_company_name') ?>"/>
        </dd>
    </dl>
    <dl class="rex-form-group form-group">
        <dt><?= $Addon->i18n('omest_shipping.pickup_street'); ?></dt>
        <dd>
            <input type="text" class="form-control" name="pickup_street" value="<?= from_array($Settings, 'pickup_street') ?>"/>
        </dd>
    </dl>
    <dl class="rex-form-group form-group">
        <dt><?= $Addon->i18n('omest_shipping.pickup_zip'); ?> *</dt>
        <dd>
            <input type="text" class="form-control" name="pickup_zip" value="<?= from_array($Settings, 'pickup_zip') ?>"/>
        </dd>
    </dl>
    <dl class="rex-form-group form-group">
        <dt><?= $Addon->i18n('omest_shipping.pickup_city'); ?></dt>
        <dd>
            <input type="text" class="form-control" name="pickup_city" value="<?= from_array($Settings, 'pickup_city') ?>"/>
        </dd>
    </dl>
    <dl class="rex-form-group form-group">
        <dt><?= $Addon->i18n('omest_shipping.pickup_country_code'); ?> *</dt>
        <dd>
            <input type="text" class="form-control" name="pickup_country_code" value="<?= from_array($Settings, 'pickup_country_code') ?>"/>
        </dd>
    </dl>
    <br/>
    <br/>


    <legend><?= $Addon->i18n('omest_shipping.api_settings'); ?></legend>
    <dl class="rex-form-group form-group">
        <dt>Test-Modus:</dt>
        <dd>
            <select class="form-control" name="sandbox">
                <option value="0"><?= $Addon->i18n('no'); ?></option>
                <option value="1" <?= from_array($Settings, 'sandbox') ? 'selected="selected"' : '' ?>><?= $Addon->i18n('yes'); ?></option>
            </select>
        </dd>
    </dl>
    <dl class="rex-form-group form-group">
        <dt>Calculate shipping costs:</dt>
        <dd>
            <select class="form-control" name="calc_costs">
                <option value="0"><?= $Addon->i18n('no'); ?></option>
                <option value="1" <?= from_array($Settings, 'calc_costs') ? 'selected="selected"' : '' ?>><?= $Addon->i18n('yes'); ?></option>
            </select>
        </dd>
    </dl>
    <dl class="rex-form-group form-group">
        <dt>Generate Shipping Barcodes:</dt>
        <dd>
            <select class="form-control" name="generate_barcode">
                <option value="0"><?= $Addon->i18n('no'); ?></option>
                <option value="1" <?= from_array($Settings, 'generate_barcode') ? 'selected="selected"' : '' ?>><?= $Addon->i18n('yes'); ?></option>
            </select>
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

</fieldset>