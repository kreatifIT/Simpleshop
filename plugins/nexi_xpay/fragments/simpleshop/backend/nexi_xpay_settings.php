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

    <legend><?= $Addon->i18n('nexi_xpay.api_settings'); ?></legend>
    <dl class="rex-form-group form-group">
        <dt>Alias:</dt>
        <dd>
            <input type="text" class="form-control" name="alias" value="<?= from_array($Settings, 'alias') ?>"/>
        </dd>
    </dl>
    <dl class="rex-form-group form-group">
        <dt>Secret:</dt>
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
                    <?= \rex_i18n::msg('nexi_xpay.use_sandbox'); ?>
                </label>
            </div>
        </dd>
    </dl>
    <dl class="rex-form-group form-group">
        <dt>SANDBOX Alias:</dt>
        <dd>
            <input type="text" class="form-control" name="sandbox_alias" value="<?= from_array($Settings, 'sandbox_alias') ?>"/>
        </dd>
    </dl>
    <dl class="rex-form-group form-group">
        <dt>SANDBOX Secret:</dt>
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
            <td>Mastercard</td>
            <td>erfolgreiche Zahlung</td>
            <td>5255000000000001</td>
            <td>12/2030</td>
            <td>jede 3 stellig Nummer</td>
        </tr>
        <tr>
            <td>Mastercard</td>
            <td>Zahlung ohne Erfolg</td>
            <td>5255000000110016</td>
            <td>12/2030</td>
            <td>jede 3 stellig Nummer</td>
        </tr>
        <tr>
            <td>Mastercard</td>
            <td>Karte abgelaufen</td>
            <td>5255000000101015</td>
            <td>12/2030</td>
            <td>jede 3 stellig Nummer</td>
        </tr>
    </table>
</fieldset>