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

    <legend><?= $Addon->i18n('settings.order_settings'); ?></legend>
    <dl class="rex-form-group form-group">
        <dt><?= $Addon->i18n('settings.order_notification_email'); ?></dt>
        <dd><input type="email" class="form-control" name="order_notification_email" value="<?= from_array($Settings, 'order_notification_email') ?>"/></dd>
    </dl>

    <?php if (DiscountGroup::isRegistered(DiscountGroup::TABLE)): ?>
    <legend><?= $Addon->i18n('settings.discount_settings'); ?></legend>
    <dl class="rex-form-group form-group">
        <dt><?= $Addon->i18n('settings.discount_application'); ?></dt>
        <dd><label class="form-label"><input type="checkbox" name="discounts_are_accumulable" value="1" <?php if (from_array($Settings, 'discounts_are_accumulable') == 1)
                {
                    echo 'checked="checked"';
                } ?>/><span></span><?= $Addon->i18n('settings.discounts_are_accumulable'); ?></label></dd>
    </dl>
    <?php endif; ?>

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