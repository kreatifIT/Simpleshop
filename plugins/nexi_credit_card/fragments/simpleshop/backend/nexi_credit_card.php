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

$Settings = $this->getVar('Settings');

?>
<fieldset>

    <legend><?= \rex_i18n::msg('nexi_credit_card.api_settings'); ?></legend>
    <dl class="rex-form-group form-group">
        <dt>TranPortalID:</dt>
        <dd>
            <input type="text" class="form-control" name="tran_portal_id" value="<?= from_array($Settings, 'tran_portal_id') ?>"/>
        </dd>
    </dl>
    <dl class="rex-form-group form-group">
        <dt>Password:</dt>
        <dd>
            <input type="text" class="form-control" name="password" value="<?= from_array($Settings, 'password') ?>"/>
        </dd>
    </dl>

    <!--    Test Credentials   -->
    <dl class="rex-form-group form-group">
        <dt>Testmodus:</dt>
        <dd>
            <div class="form-label">
                <label class="form-label">
                    <input type="checkbox" name="use_test_mode" value="1" <?= from_array($Settings, 'use_test_mode') == 1 ? 'checked="checked"' : ''; ?>/>
                    <?= \rex_i18n::msg('nexi_credit_card.use_sandbox'); ?>
                </label>
            </div>
        </dd>
    </dl>
    <dl class="rex-form-group form-group">
        <dt>TranPortal Test-ID:</dt>
        <dd>
            <input type="text" class="form-control" name="tran_portal_test_id" value="<?= from_array($Settings, 'tran_portal_test_id', 89027777) ?>"/>
        </dd>
    </dl>
    <dl class="rex-form-group form-group">
        <dt>Test-Password:</dt>
        <dd>
            <input type="text" class="form-control" name="test_password" value="<?= from_array($Settings, 'test_password', 'test') ?>"/>
        </dd>
    </dl>

</fieldset>

<fieldset>
    <legend>Test-Daten</legend>

    <table class="table">
        <tr>
            <th>Aktion</th>
            <th>CC-Nummer</th>
            <th>CC-Fälligkeit</th>
            <th>CC-CVV2</th>
        </tr>

        <tr>
            <td>erfolgreiche Zahlung</td>
            <td>4539990000000012</td>
            <td>jedes Datum in der Zukunft</td>
            <td>jede 3-4 stellig Nummer</td>
        </tr>
        <tr>
            <td>Zahlung ohne Erfolg</td>
            <td>4539990000000020</td>
            <td>jedes Datum in der Zukunft</td>
            <td>jede 3-4 stellig Nummer</td>
        </tr>
        <tr>
            <td>Zahlung mit inkorrekten Daten</td>
            <td>4999000055550000</td>
            <td>jedes Datum in der Zukunft</td>
            <td>jede 3-4 stellig Nummer</td>
        </tr>
    </table>
</fieldset>