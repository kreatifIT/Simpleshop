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

$dataId         = $this->getValue();
$addressSetting = \FriendsOfREDAXO\Simpleshop\Settings::getValue('customer_addresses_setting');

$class       = $this->getElement('required') ? 'form-is-required ' : '';
$class_group = trim('form-group ' . $class . $this->getWarningClass());

if ($addressSetting == 'disabled') {
    $label    = 'translate:label.choose_customer';
    $customer = $dataId ? \FriendsOfREDAXO\Simpleshop\Customer::get($dataId) : null;
    $object   = $customer;
    $apiUrl   = rex_url::backend('index.php?' . http_build_query(['rex-api-call' => 'simpleshop_be_api', 'controller' => 'Customer.be__searchCustomer',]));
} else {
    $label    = 'translate:label.choose_address';
    $object   = $dataId ? \FriendsOfREDAXO\Simpleshop\CustomerAddress::get($dataId) : null;
    $customer = $object ? \FriendsOfREDAXO\Simpleshop\Customer::get($object->getValue('customer_id')) : null;
    $apiUrl   = rex_url::backend('index.php?' . http_build_query(['rex-api-call' => 'simpleshop_be_api', 'controller' => 'CustomerAddress.be__searchAddress',]));
}

?>
<div class="<?= $class_group ?>">
    <label class="control-label"><?= $this->getLabel() ?></label>
    <select class="form-control address-select" name="<?= $this->getFieldName() ?>" data-placeholder="<?= rex_i18n::translate($label) ?>" data-ajax--url="<?= $apiUrl ?>" data-minimum-input-length="0" data-customer-id="<?= $customer ? $customer->getId() : null ?>">
        <?php if ($object): ?>
            <option value="<?= $object->getId() ?>" <?= $dataId == $object->getId() ? 'selected="selected"' : '' ?>>
                <?= $object->getNameForOrder() ?>
            </option>
        <?php endif; ?>
    </select>
</div>
