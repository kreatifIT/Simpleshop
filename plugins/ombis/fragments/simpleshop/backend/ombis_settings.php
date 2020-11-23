<?php

/**
 * This file is part of the Kreatif\Project package.
 *
 * @author Kreatif GmbH
 * @author a.platter@kreatif.it
 * Date: 15/06/2020
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


$Addon    = $this->getVar('Addon');
$Settings = $this->getVar('Settings');

?>
<fieldset>

    <legend><?= $Addon->i18n('paypal_express.api_settings'); ?></legend>
    <dl class="rex-form-group form-group">
        <dt>REST Base Url:</dt>
        <dd>
            <input type="text" class="form-control" name="api_base_url" value="<?= from_array($Settings, 'api_base_url') ?>" placeholder="https://domain.com:15443/rest/kreatif"/>
        </dd>
    </dl>
    <dl class="rex-form-group form-group">
        <dt>REST Company Path:</dt>
        <dd>
            <input type="text" class="form-control" name="api_company_path" value="<?= from_array($Settings, 'api_company_path') ?>" placeholder="/company"/>
        </dd>
    </dl>
    <dl class="rex-form-group form-group">
        <dt>Basic Auth Username:</dt>
        <dd>
            <input type="text" class="form-control" name="api_username" value="<?= from_array($Settings, 'api_username') ?>"/>
        </dd>
    </dl>
    <dl class="rex-form-group form-group">
        <dt>Basic Auth Password:</dt>
        <dd>
            <input type="password" class="form-control" name="api_password" value="<?= from_array($Settings, 'api_password') ?>"/>
        </dd>
    </dl>


    <legend>Order-Einstellung</legend>
    <dl class="rex-form-group form-group">
        <dt>Dummy Customer ID:</dt>
        <dd>
            <input type="text" class="form-control" name="order_dummy_id" value="<?= from_array($Settings, 'order_dummy_id') ?>"/>
        </dd>
    </dl>
    <dl class="rex-form-group form-group">
        <dt>Code für Transportspesen:</dt>
        <dd>
            <input type="text" class="form-control" name="order_shipping_code" value="<?= from_array($Settings, 'order_shipping_code') ?>"/>
        </dd>
    </dl>
    <dl class="rex-form-group form-group">
        <dt>Code für Rabattposition:</dt>
        <dd>
            <input type="text" class="form-control" name="order_disount_code" value="<?= from_array($Settings, 'order_disount_code') ?>"/>
        </dd>
    </dl>
    <dl class="rex-form-group form-group">
        <dt>Synchronisierung:</dt>
        <dd>
            <select name="ombis_order_sync" class="form-control">
                <option value="">-</option>
                    <option value="address_sync" <?= from_array($Settings, 'ombis_order_sync') == 'address_sync' ? 'selected="selected"' : '' ?>>
                        Adressen prüfen bei Bestellungsabschluss (nur Adressen syncen)
                    </option>
            </select>
        </dd>
    </dl>

    <legend><?= $Addon->i18n('label.payment_methods'); ?></legend>
    <?php
    $payments    = \FriendsOfREDAXO\Simpleshop\Payment::getAll();
    $apiPayments = \FriendsOfREDAXO\Simpleshop\Ombis\Payment::getAll();
    $values      = from_array($Settings, 'ombis_payment_config');
    ?>
    <?php if (count($apiPayments) && count($payments)): ?>
        <?php foreach ($payments as $payment): ?>
            <dl class="rex-form-group form-group">
                <dt><?= $payment->getName() ?>:</dt>
                <dd>
                    <select name="ombis_payment_config[<?= $payment->plugin_name ?>]" class="form-control">
                        <option value="">-</option>
                        <?php foreach ($apiPayments as $apiPayment): ?>
                            <option value="<?= $apiPayment['Fields']['Code'] ?>" <?= $values[$payment->plugin_name] == $apiPayment['Fields']['Code'] ? 'selected="selected"' : '' ?>>
                                <?= $apiPayment['Fields']['Name']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </dd>
            </dl>
        <?php endforeach; ?>
    <?php elseif (count($payments) == 0): ?>
        <div class="alert alert-danger">
            No payment methods active (Activate in Addons > Simpleshop)
        </div>
    <?php elseif (count($apiPayments) == 0): ?>
        <div class="alert alert-danger">
            Ombis has no registered paymentmethods (Zahlungsart) - contact client
        </div>
    <?php endif; ?>

</fieldset>