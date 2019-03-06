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

    <legend><?= $Addon->i18n('paypal_express.api_settings'); ?></legend>
    <dl class="rex-form-group form-group">
        <dt>Username:</dt>
        <dd>
            <input type="text" class="form-control" name="username" value="<?= from_array($Settings, 'username') ?>"/>
        </dd>
    </dl>
    <dl class="rex-form-group form-group">
        <dt>Password:</dt>
        <dd>
            <input type="password" class="form-control" name="password" value="<?= from_array($Settings, 'password') ?>"/>
        </dd>
    </dl>
    <dl class="rex-form-group form-group">
        <dt>Signature:</dt>
        <dd>
            <input type="text" class="form-control" name="signature" value="<?= from_array($Settings, 'signature') ?>"/>
        </dd>
    </dl>

    <!--    Test Credentials   -->
    <dl class="rex-form-group form-group">
        <dt>Sandbox:</dt>
        <dd>
            <div class="form-label">
                <label class="form-label">
                    <input type="checkbox" name="api_type" value="sandbox_" <?= from_array($Settings, 'api_type') == 'sandbox_' ? 'checked="checked"' : ''; ?>/>
                    <?= $Addon->i18n('paypal_express.use_sandbox'); ?>
                </label>
            </div>
        </dd>
    </dl>
    <dl class="rex-form-group form-group">
        <dt>SANDBOX Username:</dt>
        <dd>
            <input type="text" class="form-control" name="sandbox_username" value="<?= from_array($Settings, 'sandbox_username') ?>"/>
        </dd>
    </dl>
    <dl class="rex-form-group form-group">
        <dt>SANDBOX Password:</dt>
        <dd>
            <input type="password" class="form-control" name="sandbox_password" value="<?= from_array($Settings, 'sandbox_password') ?>"/>
        </dd>
    </dl>
    <dl class="rex-form-group form-group">
        <dt>SANDBOX Signature:</dt>
        <dd>
            <input type="text" class="form-control" name="sandbox_signature" value="<?= from_array($Settings, 'sandbox_signature') ?>"/>
        </dd>
    </dl>

</fieldset>