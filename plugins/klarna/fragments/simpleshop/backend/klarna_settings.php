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

if (empty($Settings)) {
    $Settings['sandbox_alias'] = 'PK41519_bc3892920f0e';
    $Settings['sandbox_secret'] = 'TwQt6V9BBgn4v2H0';
}
?>
<fieldset>

    <legend><?= $Addon->i18n('klarna.api_settings'); ?></legend>

    <p>
        Portal für Testmodus: <a href="https://playground.eu.portal.klarna.com/" target="_blank">https://playground.eu.portal.klarna.com/</a>
        <br/>
        Kunde muss sich auf Klarna hier registrieren (Fragebogen) <a href="https://www.klarna.com/it/commercianti/" target="_blank">https://www.klarna.com/it/commercianti/</a>
        <br/>
        <br/>
    </p>

    <dl class="rex-form-group form-group">
        <dt>API-Username:</dt>
        <dd>
            <input type="text" class="form-control" name="alias" value="<?= from_array($Settings, 'alias') ?>"/>
        </dd>
    </dl>
    <dl class="rex-form-group form-group">
        <dt>API-Passwort:</dt>
        <dd>
            <input type="text" class="form-control" name="secret" value="<?= from_array($Settings, 'secret') ?>"/>
        </dd>
    </dl>

    <!--    Test Credentials   -->
    <dl class="rex-form-group form-group">
        <dt>Testmodus:</dt>
        <dd>
            <div class="form-label">
                <label class="form-label">
                    <input type="checkbox" name="use_test_mode" value="1" <?= from_array($Settings, 'use_test_mode') == 1 ? 'checked="checked"' : ''; ?>/>
                    <?= \rex_i18n::msg('klarna.use_sandbox'); ?>
                </label>
            </div>
        </dd>
    </dl>
    <dl class="rex-form-group form-group">
        <dt>SANDBOx API-Username:</dt>
        <dd>
            <input type="text" class="form-control" name="sandbox_alias" value="<?= from_array($Settings, 'sandbox_alias') ?>"/>
        </dd>
    </dl>
    <dl class="rex-form-group form-group">
        <dt>SANDBOX API-Passwort:</dt>
        <dd>
            <input type="text" class="form-control" name="sandbox_secret" value="<?= from_array($Settings, 'sandbox_secret') ?>"/>
        </dd>
    </dl>

</fieldset>


<fieldset>
    <legend>Test-Daten</legend>

    <table class="table">
        <tr>
            <th>Aktion</th>
            <th>CC-Typ</th>
            <th>CC-Nummer</th>
            <th>CC-Fälligkeit</th>
            <th>CC-CVV2</th>
        </tr>

        <tr>
            <td>Visa</td>
            <td>erfolgreiche Zahlung</td>
            <td>4111111111111111</td>
            <td>12/2030</td>
            <td>123</td>
        </tr>
    </table>
</fieldset>