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

</fieldset>