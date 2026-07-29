<?php

/**
 * This file is part of the Simpleshop package.
 *
 * @author Kreatif GmbH
 * @author a.platter@kreatif.it
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FriendsOfREDAXO\Simpleshop;

$Settings = $this->getVar('Settings');

?>
<fieldset>
    <legend><?= \rex_i18n::msg('dfi_shipping.api_settings'); ?></legend>
    <dl class="rex-form-group form-group">
        <dt><?= \rex_i18n::msg('dfi_shipping.api_token'); ?>:</dt>
        <dd>
            <input type="text" class="form-control expanded" name="api_token" value="<?= from_array($Settings, 'api_token') ?>"/>
        </dd>
    </dl>
</fieldset>
