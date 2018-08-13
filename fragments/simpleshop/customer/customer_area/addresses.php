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


$User       = $this->getVar('User');
$action     = rex_get('action', 'string');
$canAddItem = $User->hasPermission('fragment.customer-area--addresses--add-new');

?>
<div class="member-area--address">

    <?php if ($canAddItem && $action == 'edit'): ?>
        <?php
        $address_id = rex_get('data-id', 'int');
        $back_url   = rex_getUrl();
        $Address    = $address_id ? CustomerAddress::get($address_id) : null;

        $this->setVar('Address', $Address);
        $this->setVar('back_url', $back_url);
        $this->setVar('redirect_url', $back_url);
        $this->setVar('excluded_fields', ['office_id']);
        ?>
        <h2><?= $address_id ? '###simpleshop.edit_address###' : '###simpleshop.new_address###' ?></h2>
        <?php $this->subfragment('simpleshop/customer/customer_area/address_form.php'); ?>
    <?php else: ?>
        <?php
        $where = ["customer_id = {$User->getId()}"];

        if ($User->valueIsset('addresses')) {
            $where[] = "id IN({$User->getValue('addresses')})";
        }

        $stmt = CustomerAddress::query()
            ->whereRaw('(' . implode(' OR ', $where) . ')')
            ->where('status', 0, '!=');

        $addresses = $stmt->find();
        $statuses  = \Kreatif\Utils::getArrayFromString(CustomerAddress::getYformFieldByName('status')
            ->getElement('options'));

        $this->subfragment('simpleshop/customer/customer_area/title.php');

        if (count($addresses)): ?>
            <ul class="no-bullet">U
                <?php foreach ($addresses as $address): ?>
                    <li class="row margin-small-bottom">
                        <div class="column medium-6">
                            <?= $address->getName() ?><br/>
                            <?= $address->getValue('street') ?>
                            <?= $address->valueIsset('street_additional') ? " - {$address->getValue('street_additional')}" : '' ?><br/>
                            <?= $address->getValue('postal') ?> <?= $address->getValue('location') ?><br/>
                        </div>
                        <div class="column medium-6">
                            <?php if ($canAddItem): ?>
                                <a href="<?= rex_getUrl(null, null, [
                                    'action'  => 'edit',
                                    'data-id' => $address->getId(),
                                ]) ?>">###action.edit###</a>
                            <br/>
                            <?php endif; ?>
                            <span class="badge"><?= $statuses[$address->getValue('status')] ?></span>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p class="margin-bottom">
                <i>###simpleshop.no_address_available###</i>
            </p>
        <?php endif; ?>

        <?php if ($canAddItem): ?>
            <a href="<?= rex_getUrl(null, null, ['action' => 'edit']) ?>" class="button margin-small-top">+&nbsp; ###simpleshop.add_address###</a>
        <?php endif; ?>
    <?php endif; ?>
</div>
