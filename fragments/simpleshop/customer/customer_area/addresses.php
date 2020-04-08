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
<div class="customer-area-address">

    <?php if ($canAddItem && $action == 'edit'): ?>
        <?php
        $address_id = rex_get('data-id', 'int');
        $back_url   = rex_getUrl(null, null, ['ctrl' => 'addresses']);
        $Address    = $address_id ? CustomerAddress::get($address_id) : null;

        $this->setVar('Address', $Address);
        $this->setVar('back_url', $back_url);
        $this->setVar('redirect_url', $back_url);
        $this->setVar('excluded_fields', ['office_id']);
        ?>
        <h2><?= $address_id ? '###label.edit_address###' : '###label.new_address###' ?></h2>
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

        $this->subfragment('simpleshop/customer/customer_area/title.php');

        if (count($addresses)): ?>
            <ul class="no-bullet">
                <?php foreach ($addresses as $address) {
                    $fragment = new \rex_fragment();
                    $fragment->setVar('Address', $address);
                    $fragment->setVar('canAddItem', $canAddItem);
                    echo $fragment->parse('simpleshop/customer/customer_area/address_item.php');
                }
                ?>
            </ul>
        <?php else: ?>
            <p class="margin-bottom">
                <i>###label.no_address_available###</i>
            </p>
        <?php endif; ?>

        <?php if ($canAddItem): ?>
            <a href="<?= rex_getUrl(null, null, ['action' => 'edit']) ?>" class="button margin-small-top">+&nbsp; ###simpleshop.add_address###</a>
        <?php endif; ?>
    <?php endif; ?>
</div>
