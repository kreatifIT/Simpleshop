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

$addresses  = [];
$User       = Customer::getCurrentUser();
$user_id    = $User->getValue('id');

$_addresses = CustomerAddress::query()
    ->where('customer_id', $user_id)
    ->find();

$addresses[0] = isset($_addresses[0]) ? $_addresses[0] : CustomerAddress::create();
$addresses[1] = isset($_addresses[1]) ? $_addresses[1] : CustomerAddress::create();

$yform = new \rex_yform();
$yform->addTemplatePath(\rex_path::addon('project') . 'templates');
$yform->setObjectparams('form_ytemplate', 'form,bootstrap');
$yform->setObjectparams('error_class', 'form_warning');
$yform->setObjectparams('submit_btn_show', FALSE);
$yform->setObjectparams('form_name', 'customer_address');
$yform->setObjectparams('form_action', '');
$yform->setObjectparams('form_class', 'row column');
$yform->setObjectparams('form_showformafterupdate', TRUE);


/**
 * Guest checkout
 */
$yform->setValueField('radio', [
    'name' => 'customer_address.0.salutation',
    'label' => '###label.gender###',
    'options' => '###label.female###=Miss,###label.male###=Mr',
    'default' => $addresses[0]->getValue('salutation') ?: 'Miss',
    'inline' => true
]);

$yform->setValueField('text', [
    'name' => 'customer_address.0.firstname',
    'label' => '###label.firstname###',
    'default' => $addresses[0]->getValue('firstname'),
    'required' => true
]);

$yform->setValueField('text', [
    'name' => 'customer_address.0.lastname',
    'label' => '###label.lastname###',
    'default' => $addresses[0]->getValue('lastname'),
    'required' => true
]);

$yform->setValueField('text', [
    'name' => 'customer_address.0.additional',
    'label' => '###label.addition###',
    'default' => $addresses[0]->getValue('additional'),
]);

$yform->setValueField('text', [
    'name' => 'customer_address.0.street',
    'label' => '###label.street###',
    'default' => $addresses[0]->getValue('street'),
    'required' => true
]);

$yform->setValueField('text', [
    'name' => 'customer_address.0.location',
    'label' => '###label.location###',
    'default' => $addresses[0]->getValue('location'),
    'required' => true
]);

$yform->setValueField('text', [
    'name' => 'customer_address.0.zip',
    'label' => '###label.postal###',
    'default' => $addresses[0]->getValue('zip'),
    'required' => true
]);

// TODO: add country select
/*
$yform->setValueField('select', [
    'name' => 'country',
    'label' => '###label.country###',
    'options' => '1,2,3,4,5,6,7,8,9,10',
    'required' => true
]);
*/

$yform->setValueField('text', [
    'customer_address.0.phone',
    'label' => '###label.phone###',
    'default' => $addresses[0]->getValue('phone'),
    'required' => true
]);

$yform->setValueField('radio', [
    'name' => 'customer_address.0.type',
    'label' => '###shop.client_typ###',
    'options' => '###label.private_customer###=P,###label.company###=C',
    'default' => $addresses[0]->getValue('type') ?: 'P',
    'inline' => true,
    'required' => true,
    'strong' => true
]);

$yform->setValueField('text', [
    'name' => 'customer_address.0.vat_num',
    'label' => '###label.vat_short###',
    'default' => $addresses[0]->getValue('vat_num'),
    'strong' => true
]);

$yform->setValueField('text', [
    'name' => 'customer_address.0.fiscal_code',
    'label' => '###label.fiscal_code###',
    'default' => $addresses[0]->getValue('fiscal_code'),
    'required' => true,
    'strong' => true
]);

$yform->setValueField('hidden', [
    'name' => 'customer_address.0.id',
    'value' => $addresses[0]->getValue('id')
]);

$yform->setValueField('checkbox', [
    'name' => 'use_shipping_address',
    'label' => '###shop.use_alternative_shipping_address###',
    'alternative_label' => '###shop.shipping_address###',
    'strong' => true
]);

/**
 * Alternative shipping address
 */

$yform->setValueField('html', ['opening_tag', '<div id="alternative-shipping-address" style="display: none;">']);
$yform->setValueField('text', [
    'name' => 'customer_address.1.firstname',
    'label' => '###label.firstname###',
    'default' => $addresses[1]->getValue('firstname')
]);

$yform->setValueField('text', [
    'name' => 'customer_address.1.lastname',
    'label' => '###label.lastname###',
    'default' => $addresses[1]->getValue('lastname')
]);

$yform->setValueField('text', [
    'name' => 'customer_address.1.additional',
    'label' => '###label.addition###',
    'default' => $addresses[1]->getValue('additional')
]);

$yform->setValueField('text', [
    'name' => 'customer_address.1.street',
    'label' => '###label.street###',
    'default' => $addresses[1]->getValue('street')
]);

$yform->setValueField('text', [
    'name' => 'customer_address.1.location',
    'label' => '###label.location###',
    'default' => $addresses[1]->getValue('location')
]);

$yform->setValueField('text', [
    'name' => 'customer_address.1.zip',
    'label' => '###label.postal###',
    'default' => $addresses[1]->getValue('zip')
]);

$yform->setValueField('hidden', [
    'name' => 'customer_address.1.id',
    'value' => $addresses[1]->getValue('id')
]);
$yform->setValueField('html', ['closing_tag', '</div>']);

/**
 * Submit
 */
$yform->setValueField('html', ['opening_tag', '<div class="row buttons-checkout margin-bottom">']);
$yform->setValueField('html', ['opening_tag', '<div class="medium-6 columns">']);
$yform->setValueField('html', ['back_button', '<a href="#" class="button button-gray">###action.go_back###</a>']);
$yform->setValueField('html', ['closing_tag', '</div>']);
$yform->setValueField('html', ['opening_tag', '<div class="medium-6 columns">']);

$yform->setValueField('submit', [
    'name' => 'submit',
    'labels' => '###action.go_ahead###',
    'css_classes' => 'submit-button-container'
]);
$yform->setValueField('html', ['closing_tag', '</div>']);
$yform->setValueField('html', ['closing_tag', '</div>']);

$yform->setFieldValue('send', !empty ($_POST), '', 'send');
$yform->setHiddenField('customer_id', $user_id);

// TODO: add validations
$yform->setValidateField('empty', array("customer_address.0.firstname","Bitte tragen Sie den Vornamen ein"));
$yform->setActionField('callback', ['\FriendsOfREDAXO\Simpleshop\CustomerAddress::action__save_checkout_address']);

echo $yform->getForm();