<?php

/**
 * This file is part of the Kreatif\Project package.
 *
 * @author Kreatif GmbH
 * @author a.platter@kreatif.it
 * Date: 04.11.19
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

$addressId = $this->getValue();

$addresses   = [];
$class       = $this->getElement('required') ? 'form-is-required ' : '';
$class_group = trim('form-group ' . $class . $this->getWarningClass());
$address     = $addressId ? \FriendsOfREDAXO\Simpleshop\CustomerAddress::get($addressId) : null;
$customer    = $address ? \FriendsOfREDAXO\Simpleshop\Customer::get($address->getValue('customer_id')) : null;
$apiUrl      = rex_url::frontend('index.php?' . http_build_query(['rex-api-call' => 'simpleshop_be_api', 'controller' => 'CustomerAddress.be__searchAddress',]));

?>
<div class="<?= $class_group ?>">
    <label class="control-label"><?= $this->getLabel() ?></label>
    <select class="form-control address-select" name="<?= $this->getFieldName() ?>" data-placeholder="Adresse auswählen" data-ajax--url="<?= $apiUrl ?>" data-minimum-input-length="0" data-customer-id="<?= $customer ? $customer->getId() : null ?>">
        <?php if ($address): ?>
            <option value="<?= $address->getId() ?>" <?= $addressId == $address->getId() ? 'selected="selected"' : '' ?>>
                KUNDE: [ID=<?= $customer->getId() ?>]
                <?= $customer->getName() ?>
                ---> ADRESSE: [ID=<?= $address->getId() ?>]
                <?= $address->getName() ?>
                | <?= $address->getValue('street') ?>
                | <?= $address->getValue('postal') ?>
                <?= $address->getValue('location') ?>
            </option>
        <?php endif; ?>
    </select>
</div>
