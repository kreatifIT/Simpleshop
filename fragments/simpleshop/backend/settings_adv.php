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

$Settings['coupon_use_options'] = (array)$Settings['coupon_use_options'];

$ma_contents = (array)\rex_extension::registerPoint(new \rex_extension_point('simpleshop.memberarea.input_contents', [
    'account'   => $Addon->i18n('settings.content_account'),
    'orders'    => $Addon->i18n('settings.content_history'),
    'addresses' => $Addon->i18n('settings.content_addresses'),
]));

?>
<fieldset>

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

    <legend><?= $Addon->i18n('settings.shop_settings'); ?></legend>
    <dl class="rex-form-group form-group">
        <dt><?= $Addon->i18n('settings.customer_addresses_setting'); ?></dt>
        <dd>
            <select name="customer_addresses_setting" class="form-control">
                <option value="enabled"><?= \rex_i18n::msg('label.use') ?></option>
                <option value="disabled" <?= from_array($Settings, 'customer_addresses_setting') == 'disabled' ? 'selected' : '' ?>><?= \rex_i18n::msg('label.use_not') ?></option>
            </select>
        </dd>
    </dl>
    <dl class="rex-form-group form-group">
        <dt><?= $Addon->i18n('settings.brutto_prices_info'); ?> (deprecated!)</dt>
        <dd>
            <select name="brutto_prices" class="form-control">
                <option value="0"><?= $Addon->i18n('no'); ?></option>
                <option value="1" <?= from_array($Settings, 'brutto_prices') == 1 ? 'selected' : '' ?>><?= $Addon->i18n('yes'); ?></option>
            </select>
        </dd>
    </dl>
    <dl class="rex-form-group form-group">
        <dt><?= $Addon->i18n('action.print_packing_list'); ?></dt>
        <dd>
            <select name="packing_list_printing" class="form-control">
                <option value="0"><?= $Addon->i18n('no'); ?></option>
                <option value="1" <?= from_array($Settings, 'packing_list_printing') == 1 ? 'selected' : '' ?>><?= $Addon->i18n('yes'); ?></option>
            </select>
        </dd>
    </dl>
    <dl class="rex-form-group form-group">
        <dt>Rabattaktionen aktivieren</dt>
        <dd>
            <select name="use_discount_groups" class="form-control">
                <option value="0"><?= $Addon->i18n('no'); ?></option>
                <option value="1" <?= from_array($Settings, 'use_discount_groups') == 1 ? 'selected' : '' ?>><?= $Addon->i18n('yes'); ?></option>
            </select>
        </dd>
    </dl>
    <dl class="rex-form-group form-group">
        <dt><?= $Addon->i18n('action.use_invoicing'); ?></dt>
        <dd>
            <select name="use_invoicing" class="form-control">
                <option value="0"><?= $Addon->i18n('no'); ?></option>
                <option value="1" <?= from_array($Settings, 'use_invoicing') == 1 ? 'selected' : '' ?>><?= $Addon->i18n('yes'); ?></option>
            </select>
        </dd>
    </dl>
    <dl class="rex-form-group form-group">
        <dt>Gutscheine</dt>
        <dd>
            <select name="coupon_use_options[]" class="form-control" multiple>
                <option value="global_use_single" <?= in_array('global_use_single', $Settings['coupon_use_options']) ? 'selected' : '' ?>>
                    <?= $Addon->i18n('coupon.global_use_single') ?>
                </option>
                <option value="global" <?= in_array('global', $Settings['coupon_use_options']) ? 'selected' : '' ?>>
                    <?= $Addon->i18n('coupon.use_global') ?>
                </option>
                <option value="fixedprice" <?= in_array('fixedprice', $Settings['coupon_use_options']) ? 'selected' : '' ?>>
                    <?= $Addon->i18n('coupon.use_fixedprice') ?>
                </option>
                <option value="single" <?= in_array('single', $Settings['coupon_use_options']) ? 'selected' : '' ?>>
                    <?= $Addon->i18n('coupon.use_single') ?>
                </option>
            </select>
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
        <dt><?= $Addon->i18n('settings.category_linking_input_label'); ?></dt>
        <dd>
            <select name="category_linking" class="form-control">
                <option value="0"><?= $Addon->i18n('no'); ?></option>
                <option value="1" <?= from_array($Settings, 'category_linking') == 1 ? 'selected' : '' ?>><?= $Addon->i18n('yes'); ?></option>
            </select>
        </dd>
    </dl>

</fieldset>