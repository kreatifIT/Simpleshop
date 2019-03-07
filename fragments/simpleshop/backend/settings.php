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
]));

?>
<fieldset>

    <legend><?= $Addon->i18n('settings.shop_settings'); ?></legend>
    <dl class="rex-form-group form-group">
        <dt><?= $Addon->i18n('settings.brutto_prices'); ?></dt>
        <dd>
            <label class="form-label">
                <input type="checkbox" name="brutto_prices" value="1" <?php if (from_array($Settings, 'brutto_prices') == 1) {
                    echo 'checked="checked"';
                } ?>/><span></span><?= $Addon->i18n('settings.brutto_prices_info'); ?>
            </label>
        </dd>
    </dl>
    <dl class="rex-form-group form-group">
        <dt><?= $Addon->i18n('label.packing_list_title'); ?></dt>
        <dd>
            <label class="form-label">
                <input type="checkbox" name="packing_list_printing" value="1" <?php if (from_array($Settings, 'packing_list_printing') == 1) {
                    echo 'checked="checked"';
                } ?>/><span></span><?= $Addon->i18n('action.print_packing_list'); ?>
            </label>
        </dd>
    </dl>
    <dl class="rex-form-group form-group">
        <dt><?= $Addon->i18n('label.accounting'); ?></dt>
        <dd>
            <label class="form-label">
                <input type="checkbox" name="use_invoicing" value="1" <?php if (from_array($Settings, 'use_invoicing') == 1) {
                    echo 'checked="checked"';
                } ?>/><span></span><?= $Addon->i18n('action.use_invoicing'); ?>
            </label>
        </dd>
    </dl>

    <legend><?= $Addon->i18n('settings.order_settings'); ?></legend>
    <dl class="rex-form-group form-group">
        <dt><?= $Addon->i18n('settings.order_notification_email'); ?></dt>
        <dd><input type="email" class="form-control" name="order_notification_email" value="<?= from_array($Settings, 'order_notification_email') ?>"/></dd>
    </dl>

    <?php if (DiscountGroup::isRegistered(DiscountGroup::TABLE)): ?>
        <legend><?= $Addon->i18n('settings.discount_settings'); ?></legend>
        <dl class="rex-form-group form-group">
            <dt><?= $Addon->i18n('settings.discount_application'); ?></dt>
            <dd><label class="form-label"><input type="checkbox" name="discounts_are_accumulable" value="1" <?php if (from_array($Settings, 'discounts_are_accumulable') == 1) {
                        echo 'checked="checked"';
                    } ?>/><span></span><?= $Addon->i18n('settings.discounts_are_accumulable'); ?></label></dd>
        </dl>
    <?php endif; ?>

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

    <!--    <br/>-->
    <!---->
    <!--    <legend>--><? //= $Addon->i18n('url_settings');
    ?><!--</legend>-->
    <!--    <p>--><? //= $Addon->i18n('setup_column_creating_text');
    ?><!--</p>-->
    <!--    <div class="row">-->
    <!--        <div class="col-sm-12">-->
    <!--            <div class="rex-select-style">-->
    <!--                --><? //= \rex_var_linklist::getWidget(1, 'test', '')
    ?>
    <!--            </div>-->
    <!--        </div>-->
    <!--    </div>-->

</fieldset>