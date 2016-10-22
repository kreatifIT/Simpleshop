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

$yform   = $this->getVar('yform');
$extras  = $this->getVar('extras');
$address = $this->getVar('address');


if (Session::getCheckoutData('as_guest') || !Customer::isLoggedIn())
{
    Session::setCheckoutData('as_guest', TRUE);
    /**
     * Guest checkout
     */
    $yform->setValueField('text', [
        'name'     => 'email',
        'label'    => '###label.email###',
        'default'  => $extras['email'],
        'required' => TRUE,
    ]);
    $yform->setValidateField('empty', [
        'email',
        strtr(\Sprog\Wildcard::get('error.field_empty'), ['{{fieldname}}' => '###label.email###']),
    ]);
    $yform->setValidateField('email', [
        'email',
        '###error.email_not_valid###',
    ]);
}


/**
 * Invoice address
 */
$yform->setValueField('radio', [
    'name'    => 'customer_address.1.salutation',
    'label'   => '###label.gender###',
    'options' => '###label.female###=Miss,###label.male###=Mr',
    'default' => $address->getValue('salutation') ?: 'Miss',
    'inline'  => TRUE,
]);

$yform->setValueField('text', [
    'name'     => 'customer_address.1.firstname',
    'label'    => '###label.firstname###',
    'default'  => $address->getValue('firstname'),
    'required' => TRUE,
]);
$yform->setValidateField('empty', [
    'customer_address.1.firstname',
    strtr(\Sprog\Wildcard::get('error.field_empty'), ['{{fieldname}}' => '###label.firstname###']),
]);

$yform->setValueField('text', [
    'name'     => 'customer_address.1.lastname',
    'label'    => '###label.lastname###',
    'default'  => $address->getValue('lastname'),
    'required' => TRUE,
]);
$yform->setValidateField('empty', [
    'customer_address.1.lastname',
    strtr(\Sprog\Wildcard::get('error.field_empty'), ['{{fieldname}}' => '###label.lastname###']),
]);

$yform->setValueField('text', [
    'name'    => 'customer_address.1.additional',
    'label'   => '###label.addition###',
    'default' => $address->getValue('additional'),
]);

$yform->setValueField('text', [
    'name'     => 'customer_address.1.street',
    'label'    => '###label.street###',
    'default'  => $address->getValue('street'),
    'required' => TRUE,
]);
$yform->setValidateField('empty', [
    'customer_address.1.street',
    strtr(\Sprog\Wildcard::get('error.field_empty'), ['{{fieldname}}' => '###label.street###']),
]);


$yform->setValueField('text', [
    'name'     => 'customer_address.1.location',
    'label'    => '###label.location###',
    'default'  => $address->getValue('location'),
    'required' => TRUE,
]);
$yform->setValidateField('empty', [
    'customer_address.1.location',
    strtr(\Sprog\Wildcard::get('error.field_empty'), ['{{fieldname}}' => '###label.location###']),
]);

$yform->setValueField('text', [
    'name'     => 'customer_address.1.zip',
    'label'    => '###label.postal###',
    'default'  => $address->getValue('zip'),
    'required' => TRUE,
]);
$yform->setValidateField('empty', [
    'customer_address.1.zip',
    strtr(\Sprog\Wildcard::get('error.field_empty'), ['{{fieldname}}' => '###label.postal###']),
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
    'name'     => 'customer_address.1.phone',
    'label'    => '###label.phone###',
    'default'  => $address->getValue('phone'),
    'required' => TRUE,
]);
$yform->setValidateField('empty', [
    'customer_address.1.phone',
    strtr(\Sprog\Wildcard::get('error.field_empty'), ['{{fieldname}}' => '###label.phone###']),
]);

//$yform->setValueField('radio', [
//    'name' => 'customer_address.1.type',
//    'label' => '###shop.client_typ###',
//    'options' => '###label.private_customer###=P,###label.company###=C',
//    'default' => $address->getValue('type') ?: 'P',
//    'inline' => true,
//    'required' => true,
//    'strong' => true
//]);
//
//$yform->setValueField('text', [
//    'name' => 'customer_address.1.vat_num',
//    'label' => '###label.vat_short###',
//    'default' => $address->getValue('vat_num'),
//    'strong' => true
//]);

$yform->setValueField('text', [
    'name'     => 'customer_address.1.fiscal_code',
    'label'    => '###label.fiscal_code###',
    'default'  => $address->getValue('fiscal_code'),
    'required' => TRUE,
    'strong'   => TRUE,
]);

//$yform->setValueField('hidden', [
//    'name' => 'customer_address.1.id',
//    'value' => $address->getValue('id'),
//]);
$yform->setHiddenField('id_1', $address->getValue('id'));

