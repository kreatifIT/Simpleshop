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

use \FriendsOfREDAXO\Simpleshop;
use \FriendsOfREDAXO\Simpleshop\Ombis;


$Addon    = $this->getVar('Addon');
$Settings = $this->getVar('Settings');


$payments        = Simpleshop\Payment::getAll(['ID', 'Name']);
$apiPayments     = Ombis\Payment::getAll(['ID', 'Code', 'Name']);
$apiSammelKonto  = Ombis\Customer\Sammelkontogruppe::getAll();
$apiBuchungsgrp  = Ombis\Customer\Buchungsgruppe::getAll(['ID', 'Name']);
$apiMwstgruppe   = Ombis\Customer\Mwstgruppe::getAll(['ID', 'Name']);
$apiBranche      = Ombis\Customer\Branche::getAll(['ID', 'Name']);
$apiVerkaufer    = Ombis\Customer\Verkaeufer::getAll(['ID', 'DisplayName']);
$apiKontingent   = Ombis\Customer\Kontingentgebiet::getAll(['ID', 'Name']);
$apiStatistikgrp = Ombis\Customer\Statistikgruppe::getAll(['ID', 'Name']);
$apiPaymentTerms = Ombis\Customer\Zahlungsbedingungen::getAll(['ID', 'Name']);

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
                <option value="customer_sync" <?= from_array($Settings, 'ombis_order_sync') == 'customer_sync' ? 'selected="selected"' : '' ?>>
                    Kunde bei Bestellungsabschluss erstellen oder aktualisieren
                </option>
            </select>
        </dd>
    </dl>

    <legend>Zahlungsmethoden</legend>
    <?php if (count($apiPayments) && count($payments)): ?>
        <?php
        $values = from_array($Settings, 'ombis_payment_config');
        ?>
        <?php foreach ($payments as $payment): ?>
            <dl class="rex-form-group form-group">
                <dt><?= $payment->getName() ?>:</dt>
                <dd>
                    <select name="ombis_payment_config[<?= $payment->plugin_name ?>]" class="form-control">
                        <option value="">-</option>
                        <?php foreach ($apiPayments as $apiPayment): ?>
                            <option value="<?= $apiPayment['Fields']['ID'] ?>" <?= $values[$payment->plugin_name] == $apiPayment['Fields']['ID'] ? 'selected="selected"' : '' ?>>
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

    <legend>Kunden-Settings</legend>

    <?php if (count($apiSammelKonto)): ?>
        <?php
        $values = from_array($Settings, 'ombis_customer_settings');
        ?>
        <dl class="rex-form-group form-group">
            <dt>Sammelkontogruppe:</dt>
            <dd>
                <select name="ombis_customer_settings[sammelkontogruppe]" class="form-control">
                    <option value="">-</option>
                    <?php foreach ($apiSammelKonto as $item): ?>
                        <option value="<?= $item['Fields']['ID'] ?>" <?= $values['sammelkontogruppe'] == $item['Fields']['ID'] ? 'selected="selected"' : '' ?>>
                            <?= $item['Fields']['Name']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </dd>
        </dl>
    <?php endif; ?>

    <?php if (count($apiBuchungsgrp)): ?>
        <?php
        $values = from_array($Settings, 'ombis_customer_settings');
        ?>
        <dl class="rex-form-group form-group">
            <dt>Buchungsgruppe:</dt>
            <dd>
                <select name="ombis_customer_settings[buchungsgruppe]" class="form-control">
                    <option value="">-</option>
                    <?php foreach ($apiBuchungsgrp as $item): ?>
                        <option value="<?= $item['Fields']['ID'] ?>" <?= $values['buchungsgruppe'] == $item['Fields']['ID'] ? 'selected="selected"' : '' ?>>
                            <?= $item['Fields']['Name']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </dd>
        </dl>
    <?php endif; ?>

    <?php if (count($apiBranche)): ?>
        <?php
        $values = from_array($Settings, 'ombis_customer_settings');
        ?>
        <dl class="rex-form-group form-group">
            <dt>Branche:</dt>
            <dd>
                <select name="ombis_customer_settings[branche]" class="form-control">
                    <option value="">-</option>
                    <?php foreach ($apiBranche as $item): ?>
                        <option value="<?= $item['Fields']['ID'] ?>" <?= $values['branche'] == $item['Fields']['ID'] ? 'selected="selected"' : '' ?>>
                            <?= $item['Fields']['Name']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </dd>
        </dl>
    <?php endif; ?>

    <?php if (count($apiVerkaufer)): ?>
        <?php
        $values = from_array($Settings, 'ombis_customer_settings');
        ?>
        <dl class="rex-form-group form-group">
            <dt>Verkäufer:</dt>
            <dd>
                <select name="ombis_customer_settings[seller]" class="form-control">
                    <option value="">-</option>
                    <?php foreach ($apiVerkaufer as $item): ?>
                        <option value="<?= $item['Fields']['ID'] ?>" <?= $values['seller'] == $item['Fields']['ID'] ? 'selected="selected"' : '' ?>>
                            <?= $item['Fields']['DisplayName']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </dd>
        </dl>
    <?php endif; ?>

    <?php if (count($apiKontingent)): ?>
        <?php
        $values = from_array($Settings, 'ombis_customer_settings');
        ?>
        <dl class="rex-form-group form-group">
            <dt>Kontingentgebiet:</dt>
            <dd>
                <select name="ombis_customer_settings[kontingentgebiet]" class="form-control">
                    <option value="">-</option>
                    <?php foreach ($apiKontingent as $item): ?>
                        <option value="<?= $item['Fields']['ID'] ?>" <?= $values['kontingentgebiet'] == $item['Fields']['ID'] ? 'selected="selected"' : '' ?>>
                            <?= $item['Fields']['Name']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </dd>
        </dl>
    <?php endif; ?>

    <?php if (count($apiMwstgruppe)): ?>
        <?php
        $values = from_array($Settings, 'ombis_tax_group');
        ?>
        <dl class="rex-form-group form-group">
            <dt>MwstGruppe Default:</dt>
            <dd>
                <select name="ombis_tax_group[default]" class="form-control">
                    <option value="">-</option>
                    <?php foreach ($apiMwstgruppe as $item): ?>
                        <option value="<?= $item['Fields']['ID'] ?>" <?= $values['default'] == $item['Fields']['ID'] ? 'selected="selected"' : '' ?>>
                            <?= $item['Fields']['Name']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </dd>
        </dl>
        <dl class="rex-form-group form-group">
            <dt>MwstGruppe ITALIEN:</dt>
            <dd>
                <select name="ombis_tax_group[93]" class="form-control">
                    <option value="">-</option>
                    <?php foreach ($apiMwstgruppe as $item): ?>
                        <option value="<?= $item['Fields']['ID'] ?>" <?= $values[93] == $item['Fields']['ID'] ? 'selected="selected"' : '' ?>>
                            <?= $item['Fields']['Name']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </dd>
        </dl>
    <?php endif; ?>

    <?php if (count($apiStatistikgrp)): ?>
        <?php
        $values = from_array($Settings, 'ombis_statistic_group');
        ?>
        <dl class="rex-form-group form-group">
            <dt>Statistikgruppe Südtirol:</dt>
            <dd>
                <select name="ombis_statistic_group[southtyrol]" class="form-control">
                    <option value="">-</option>
                    <?php foreach ($apiStatistikgrp as $item): ?>
                        <option value="<?= $item['Fields']['ID'] ?>" <?= $values['southtyrol'] == $item['Fields']['ID'] ? 'selected="selected"' : '' ?>>
                            <?= $item['Fields']['Name']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </dd>
        </dl>

        <dl class="rex-form-group form-group">
            <dt>Statistikgruppe Italien:</dt>
            <dd>
                <select name="ombis_statistic_group[93]" class="form-control">
                    <option value="">-</option>
                    <?php foreach ($apiStatistikgrp as $item): ?>
                        <option value="<?= $item['Fields']['ID'] ?>" <?= $values[93] == $item['Fields']['ID'] ? 'selected="selected"' : '' ?>>
                            <?= $item['Fields']['Name']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </dd>
        </dl>

        <dl class="rex-form-group form-group">
            <dt>Statistikgruppe Ausland:</dt>
            <dd>
                <select name="ombis_statistic_group[default]" class="form-control">
                    <option value="">-</option>
                    <?php foreach ($apiStatistikgrp as $item): ?>
                        <option value="<?= $item['Fields']['ID'] ?>" <?= $values['default'] == $item['Fields']['ID'] ? 'selected="selected"' : '' ?>>
                            <?= $item['Fields']['Name']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </dd>
        </dl>
    <?php endif; ?>

    <?php if ($apiPaymentTerms): ?>
        <?php
        $values = from_array($Settings, 'ombis_customer_settings');
        ?>
        <dl class="rex-form-group form-group">
            <dt>Zahlungsbedingungen:</dt>
            <dd>
                <select name="ombis_customer_settings[zahlungsbedingungen]" class="form-control">
                    <option value="">-</option>
                    <?php foreach ($apiPaymentTerms as $item): ?>
                        <option value="<?= $item['Fields']['ID'] ?>" <?= $values['zahlungsbedingungen'] == $item['Fields']['ID'] ? 'selected="selected"' : '' ?>>
                            <?= $item['Fields']['Name']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </dd>
        </dl>
    <?php endif; ?>

</fieldset>