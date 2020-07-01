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
$output     = '';

if ($canAddItem && $action == 'edit') {
    $fragment = new \rex_fragment();
    $output   = $fragment->parse('simpleshop/customer/customer_area/address_edit.php');
} else {
    $addresses = $User->getShippingAddresses();
}

?>
<div class="customer-area-address">

    <?php if ($output): ?>
        <?= $output ?>
    <?php else: ?>
        <h2 class="margin-small-bottom">
            ###label.addresses###
        </h2>

        <?php
        if (count($addresses)): ?>
            <div class="address-list">
                <?php foreach ($addresses as $address): ?>
                    <?php
                    $addressData = $address->toAddressArray();
                    ?>
                    <div class="address-item grid-x grid-margin-x">
                        <div class="cell medium-6">
                            <?= implode('<br/>', $addressData) ?>
                        </div>
                        <div class="cell medium-6">
                            <?php if ($canAddItem): ?>
                                <a class="link" href="<?= rex_getUrl(null, null, [
                                    'action'  => 'edit',
                                    'ctrl'    => 'addresses.detail',
                                    'data-id' => $address->getId(),
                                ]) ?>">###action.edit###</a>
                                <br/>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="no-address">###label.no_address_available###</div>
        <?php endif; ?>

        <?php if ($canAddItem): ?>
            <a href="<?= rex_getUrl(null, null, ['ctrl' => 'addresses.edit', 'action' => 'edit']) ?>" class="button margin-small-top">+&nbsp; ###action.add_address###</a>
        <?php endif; ?>
    <?php endif; ?>
</div>
