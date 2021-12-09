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

$ma_contents = (array)\rex_extension::registerPoint(new \rex_extension_point('simpleshop.memberarea.input_contents', [
    'account'   => $Addon->i18n('settings.content_account'),
    'orders'    => $Addon->i18n('settings.content_history'),
    'addresses' => $Addon->i18n('settings.content_addresses'),
    'rma'       => $Addon->i18n('settings.content_rma'),
]));

?>
<fieldset>

    <legend><?= $Addon->i18n('settings.shop_settings'); ?></legend>
    <dl class="rex-form-group form-group">
        <dt><?= $Addon->i18n('settings.brutto_prices'); ?> (deprecated!)</dt>
        <dd>
            <label class="form-label">
                <input type="checkbox" name="brutto_prices" value="1" <?= from_array($Settings, 'brutto_prices') == 1 ? 'checked="checked"' : '' ?>/>
                <span></span><?= $Addon->i18n('settings.brutto_prices_info'); ?>
            </label>
        </dd>
    </dl>
    <dl class="rex-form-group form-group">
        <dt><?= $Addon->i18n('label.packing_list_title'); ?></dt>
        <dd>
            <label class="form-label">
                <input type="checkbox" name="packing_list_printing" value="1" <?= from_array($Settings, 'packing_list_printing') == 1 ? 'checked="checked"' : '' ?>/>
                <span></span><?= $Addon->i18n('action.print_packing_list'); ?>
            </label>
        </dd>
    </dl>
    <dl class="rex-form-group form-group">
        <dt><?= $Addon->i18n('label.accounting'); ?></dt>
        <dd>
            <label class="form-label">
                <input type="checkbox" name="use_invoicing" value="1" <?= from_array($Settings, 'use_invoicing') == 1 ? 'checked="checked"' : '' ?>/>
                <span></span>
                <?= $Addon->i18n('action.use_invoicing'); ?>
            </label>
        </dd>
    </dl>
    <dl class="rex-form-group form-group">
        <dt>Rabattfunktion</dt>
        <dd>
            <label class="form-label">
                <input type="checkbox" name="not_apply_discounts_to_shipping" value="1" <?= from_array($Settings, 'not_apply_discounts_to_shipping') == 1 ? 'checked="checked"' : '' ?>/>
                <span></span>
                Rabatte Versand NICHT anwenden
            </label>
        </dd>
    </dl>

    <br>
    <br>

    <legend><?= $Addon->i18n('settings.page_mapping'); ?></legend>
    <dl class="rex-form-group form-group">
        <dt>Kunden-Dashboard</dt>
        <dd>
            <?= \rex_var_link::getWidget('linklist_dashboard', 'linklist[dashboard]', $Settings['linklist']['dashboard']) ?>
        </dd>
    </dl>
    <dl class="rex-form-group form-group">
        <dt>Warenkorb</dt>
        <dd>
            <?= \rex_var_link::getWidget('linklist_cart', 'linklist[cart]', $Settings['linklist']['cart']) ?>
        </dd>
    </dl>
    <dl class="rex-form-group form-group">
        <dt>Checkout</dt>
        <dd>
            <?= \rex_var_link::getWidget('linklist_checkout', 'linklist[checkout]', $Settings['linklist']['checkout']) ?>
        </dd>
    </dl>

    <br>
    <br>

    <legend><?= $Addon->i18n('settings.member_area_contents'); ?></legend>
    <dl class="rex-form-group form-group">
        <dt><?= $Addon->i18n('settings.member_area_contents_label'); ?></dt>
        <dd>
            <select name="membera_area_contents[]" class="form-control" size="6" multiple>
                <?php foreach ($ma_contents as $key => $value): ?>
                    <option value="<?= $key ?>" <?= in_array($key, (array)$Settings['membera_area_contents']) ? 'selected="selected"' : '' ?>><?= $value ?></option>
                <?php endforeach; ?>
            </select>
        </dd>
    </dl>

    <br>
    <br>

    <legend><?= $Addon->i18n('settings.shop_functions'); ?></legend>
    <dl class="rex-form-group form-group">
        <dt><?= $Addon->i18n('settings.category_linking_label'); ?></dt>
        <dd>
            <label class="form-label">
                <input type="checkbox" name="category_linking" value="1" <?= from_array($Settings, 'category_linking') == 1 ? 'checked="checked"' : '' ?>/>
                <span></span>
                <?= $Addon->i18n('settings.category_linking_input_label'); ?>
            </label>
        </dd>
    </dl>

</fieldset>