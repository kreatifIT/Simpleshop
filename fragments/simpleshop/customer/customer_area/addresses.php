<?php

/**
 * This file is part of the Kreatif\Project package.
 *
 * @author Kreatif GmbH
 * @author a.platter@kreatif.it
 * Date: 07.08.18
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace FriendsOfREDAXO\Simpleshop;


$User  = $this->getVar('User');
$title = $this->getVar('title', '###label.account_data###');
$text  = $this->getVar('text');

$action = rex_get('action', 'string');

?>
<div class="member-area--address">

    <?php if ($action == 'edit'): ?>
        <?php
        $address_id = rex_get('data-id', 'int');
        $back_url   = rex_getUrl();
        $Address    = $address_id ? CustomerAddress::get($address_id) : null;

        $this->setVar('Address', $Address);
        $this->setVar('back_url', $back_url);
        $this->setVar('redirect_url', $back_url);
        ?>
        <h2><?= $address_id ? '###simpleshop.edit_address###' : '###simpleshop.new_address###' ?></h2>
        <?php $this->subfragment('simpleshop/customer/customer_area/address_form.php'); ?>
    <?php else: ?>
        <div class="row column">
            <h2 class="<?= strlen($text) ? 'margin-bottom' : '' ?>"><?= $title ?></h2>
            <?= strlen($text) ? $text : '' ?>
        </div>

        <?php
        $addresses = CustomerAddress::query()
            ->where('customer_id', $User->getId())
            ->find();

        if (count($addresses)): ?>
            <ul class="no-bullet">
                <?php foreach ($addresses as $address): ?>
                    <li class="row margin-small-top">
                        <div class="column medium-6">
                            <?= $address->getName() ?><br/>
                            <?= $address->getValue('street') ?>
                            <?= $address->valueIsset('street_additional') ? " - {$address->getValue('street_additional')}" : '' ?><br/>
                            <?= $address->getValue('postal') ?> <?= $address->getValue('location') ?><br/>
                        </div>
                        <div class="column medium-6">
                            <a href="<?= rex_getUrl(null, null, [
                                'action'  => 'edit',
                                'data-id' => $address->getId(),
                            ]) ?>">###action.edit###</a>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p class="margin-small-top margin-bottom">
                <i>###simpleshop.no_address_available###</i>
            </p>
        <?php endif; ?>

        <a href="<?= rex_getUrl(null, null, ['action' => 'edit']) ?>" class="button margin-small-top">+&nbsp; ###simpleshop.add_address###</a>
    <?php endif; ?>
</div>
