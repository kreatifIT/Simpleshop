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

    <legend><?= $Addon->i18n('omest_shipping.api_settings'); ?></legend>
    <dl class="rex-form-group form-group">
        <dt>Api-Key:</dt>
        <dd>
            <label class="form-label">
                <input type="text" class="form-control" name="api_key" value="<?= from_array($Settings, 'api_key') ?>"/>
            </label>
        </dd>
    </dl>
    <dl class="rex-form-group form-group">
        <dt>Customer-Key:</dt>
        <dd>
            <label class="form-label">
                <input type="text" class="form-control" name="customer_key" value="<?= from_array($Settings, 'customer_key') ?>"/>
            </label>
        </dd>
    </dl>
    <dl class="rex-form-group form-group">
        <dt>TEST Api-Key:</dt>
        <dd>
            <label class="form-label">
                <input type="text" class="form-control" name="test_api_key" value="<?= from_array($Settings, 'test_api_key') ?>"/>
            </label>
        </dd>
    </dl>
    <dl class="rex-form-group form-group">
        <dt>TEST Customer-Key:</dt>
        <dd>
            <label class="form-label">
                <input type="text" class="form-control" name="test_customer_key" value="<?= from_array($Settings, 'test_customer_key') ?>"/>
            </label>
        </dd>
    </dl>

</fieldset>