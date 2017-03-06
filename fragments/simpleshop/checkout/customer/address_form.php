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

global $yform;

$extras    = [];
$addresses = [];
$user_id   = null;

$show_address_as_list    = $this->getVar('show_address_as_list', false);
$has_shipping            = $this->getVar('has_shipping', true);
$show_back_and_ahead_btn = $this->getVar('show_back_and_ahead_btn', true);

if (Session::getCheckoutData('Order')) {
    $Order         = Session::getCurrentOrder();
    $_addresses[0] = $Order->getValue('address_1');
    $_addresses[1] = $Order->getValue('address_2');
    $extras        = $Order->getValue('extras');
}
if (Customer::isLoggedIn()) {
    $User       = Customer::getCurrentUser();
    $user_id    = $User->getValue('id');
    $_addresses = CustomerAddress::query()->where('customer_id', $user_id)->find();
}
$countries    = ['-' => '- ###shop.choose_country### -'];
$addresses[0] = isset($_addresses[0]) && !empty($_addresses[0]) ? $_addresses[0] : CustomerAddress::create();
$addresses[1] = isset($_addresses[1]) && !empty($_addresses[1]) ? $_addresses[1] : CustomerAddress::create();
$_countries   = Country::query()->where('status', 1)->orderBy('prio', 'asc')->find();

foreach ($_countries as $country) {
    $countries[$country->getValue('code')] = $country->getValue(sprogfield('name'));
}

$yform = new \rex_yform();
$yform->addTemplatePath(\rex_path::addon('project') . 'templates');
$yform->setObjectparams('form_ytemplate', 'form,bootstrap');
$yform->setObjectparams('error_class', 'form_warning');
$yform->setObjectparams('submit_btn_show', false);
$yform->setObjectparams('form_name', 'customer_address');
$yform->setObjectparams('form_action', '');
$yform->setObjectparams('form_class', 'row column');
$yform->setObjectparams('form_showformafterupdate', true);


if ($show_address_as_list) {
    $yform->setValueField('html', ['opening_tag', '<h2>###shop.invoice_address###']);
    $yform->setValueField('html', ['closing_tag', '</h2>']);
}

if (Session::getCheckoutData('as_guest') || !Customer::isLoggedIn()) {
    Session::setCheckoutData('as_guest', true);
    /**
     * Guest checkout
     */
    $yform->setValueField('email', [
        'name'     => 'email',
        'label'    => '###label.email###',
        'default'  => $extras['address_extras']['email'],
        'required' => true,
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
    'default' => $addresses[0]->getValue('salutation') ?: 'Miss',
    'inline'  => true,
]);

$yform->setValueField('text', [
    'name'     => 'customer_address.1.firstname',
    'label'    => '###label.firstname###',
    'default'  => $addresses[0]->getValue('firstname'),
    'required' => true,
]);
$yform->setValidateField('empty', [
    'customer_address.1.firstname',
    strtr(\Sprog\Wildcard::get('error.field_empty'), ['{{fieldname}}' => '###label.firstname###']),
]);

$yform->setValueField('text', [
    'name'     => 'customer_address.1.lastname',
    'label'    => '###label.lastname###',
    'default'  => $addresses[0]->getValue('lastname'),
    'required' => true,
]);
$yform->setValidateField('empty', [
    'customer_address.1.lastname',
    strtr(\Sprog\Wildcard::get('error.field_empty'), ['{{fieldname}}' => '###label.lastname###']),
]);

$yform->setValueField('text', [
    'name'    => 'customer_address.1.additional',
    'label'   => '###label.addition###',
    'default' => $addresses[0]->getValue('additional'),
]);

$yform->setValueField('text', [
    'name'     => 'customer_address.1.street',
    'label'    => '###label.street###',
    'default'  => $addresses[0]->getValue('street'),
    'required' => true,
]);
$yform->setValidateField('empty', [
    'customer_address.1.street',
    strtr(\Sprog\Wildcard::get('error.field_empty'), ['{{fieldname}}' => '###label.street###']),
]);


$yform->setValueField('text', [
    'name'     => 'customer_address.1.location',
    'label'    => '###label.location###',
    'default'  => $addresses[0]->getValue('location'),
    'required' => true,
]);
$yform->setValidateField('empty', [
    'customer_address.1.location',
    strtr(\Sprog\Wildcard::get('error.field_empty'), ['{{fieldname}}' => '###label.location###']),
]);

$yform->setValueField('text', [
    'name'     => 'customer_address.1.zip',
    'label'    => '###label.postal###',
    'default'  => $addresses[0]->getValue('zip'),
    'required' => true,
]);
$yform->setValidateField('empty', [
    'customer_address.1.zip',
    strtr(\Sprog\Wildcard::get('error.field_empty'), ['{{fieldname}}' => '###label.postal###']),
]);

$yform->setValueField('select', [
    'name'     => 'customer_address.1.country',
    'label'    => '###label.country###',
    'options'  => $countries,
    'default'  => $addresses[0]->getValue('country'),
    'required' => true,
]);
$yform->setValidateField('empty', [
    'customer_address.1.country',
    strtr(\Sprog\Wildcard::get('error.field_empty'), ['{{fieldname}}' => '###label.country###']),
]);
$yform->setValidateField('compare_value', [
    'name'          => 'customer_address.1.country',
    'compare_type'  => '==',
    'compare_value' => '-',
    'message'       => strtr(\Sprog\Wildcard::get('error.field_empty'), ['{{fieldname}}' => '###label.country###']),
]);


$yform->setValueField('text', [
    'name'     => 'customer_address.1.phone',
    'label'    => '###label.phone###',
    'default'  => $addresses[0]->getValue('phone'),
    'required' => true,
]);
$yform->setValidateField('empty', [
    'customer_address.1.phone',
    strtr(\Sprog\Wildcard::get('error.field_empty'), ['{{fieldname}}' => '###label.phone###']),
]);

//$yform->setValueField('radio', [
//    'name' => 'customer_address.1.type',
//    'label' => '###shop.client_typ###',
//    'options' => '###label.private_customer###=P,###label.company###=C',
//    'default' => $addresses[0]->getValue('type') ?: 'P',
//    'inline' => true,
//    'required' => true,
//    'strong' => true
//]);
//
//$yform->setValueField('text', [
//    'name' => 'customer_address.1.vat_num',
//    'label' => '###label.vat_short###',
//    'default' => $addresses[0]->getValue('vat_num'),
//    'strong' => true
//]);

$yform->setValueField('text', [
    'name'     => 'customer_address.1.fiscal_code',
    'label'    => '###label.fiscal_code###',
    'default'  => $addresses[0]->getValue('fiscal_code'),
    'required' => false,
    'strong'   => true,
]);

//$yform->setValueField('hidden', [
//    'name' => 'customer_address.1.id',
//    'value' => $addresses[0]->getValue('id'),
//]);
$yform->setHiddenField('id_1', $addresses[0]->getValue('id'));


if ($has_shipping) {
    /**
     * Alternative shipping address
     */
    if ($show_address_as_list) {
        $yform->setValueField('html', ['opening_tag', '<div class="shipping-address padding-top"><h2>###shop.shipping_address###</h2>']);
    }
    else {
        $yform->setValueField('checkbox', [
            'name'              => 'use_shipping_address',
            'label'             => '###shop.use_alternative_shipping_address###',
            'alternative_label' => '###shop.shipping_address###',
            'default'           => $extras['address_extras']['use_shipping_address'],
            'strong'            => true,
        ]);

        $visibility_class = $extras['address_extras']['use_shipping_address'] ?: 'style="display: none;"';
        $yform->setValueField('html', ['opening_tag', '<div id="alternative-shipping-address" ' . $visibility_class . '>']);
    }

    $yform->setValueField('text', [
        'name'    => 'customer_address.2.firstname',
        'label'   => '###label.firstname###',
        'default' => $addresses[1]->getValue('firstname'),
    ]);

    $yform->setValueField('text', [
        'name'    => 'customer_address.2.lastname',
        'label'   => '###label.lastname###',
        'default' => $addresses[1]->getValue('lastname'),
    ]);

    $yform->setValueField('text', [
        'name'    => 'customer_address.2.additional',
        'label'   => '###label.addition###',
        'default' => $addresses[1]->getValue('additional'),
    ]);

    $yform->setValueField('text', [
        'name'    => 'customer_address.2.street',
        'label'   => '###label.street###',
        'default' => $addresses[1]->getValue('street'),
    ]);

    $yform->setValueField('text', [
        'name'    => 'customer_address.2.location',
        'label'   => '###label.location###',
        'default' => $addresses[1]->getValue('location'),
    ]);

    $yform->setValueField('text', [
        'name'    => 'customer_address.2.zip',
        'label'   => '###label.postal###',
        'default' => $addresses[1]->getValue('zip'),
    ]);

    array_shift($countries);
    $yform->setValueField('select', [
        'name'    => 'customer_address.2.country',
        'label'   => '###label.country###',
        'options' => $countries,
        'default' => $addresses[1]->getValue('country'),
    ]);

    $yform->setHiddenField('id_2', $addresses[1]->getValue('id'));

    $yform->setValueField('html', ['closing_tag', '</div>']);
}


/**
 * Submit
 */

if ($show_back_and_ahead_btn) {
    $yform->setValueField('html', ['opening_tag', '<div class="row buttons-checkout margin-bottom">']);
    $yform->setValueField('html', ['opening_tag', '<div class="medium-6 columns">']);
    $yform->setValueField('html', ['back_button', '<a href="#" class="button button-gray">###action.go_back###</a>']);
    $yform->setValueField('html', ['closing_tag', '</div>']);
    $yform->setValueField('html', ['opening_tag', '<div class="medium-6 columns">']);

    $yform->setValueField('submit', [
        'name'        => 'submit',
        'labels'      => '###action.go_ahead###',
        'css_classes' => 'submit-button-container',
    ]);
}
else {
    $yform->setValueField('html', ['opening_tag', '<div class="row buttons-checkout margin-top">']);
    $yform->setValueField('submit', [
        'name'        => 'submit',
        'labels'      => '###action.save###',
        'css_classes' => 'submit-button-container',
    ]);
    $yform->setValueField('html', ['closing_tag', '</div>']);
}

$yform->setValueField('html', ['closing_tag', '</div>']);
$yform->setValueField('html', ['closing_tag', '</div>']);

$yform->setFieldValue('send', !empty ($_POST), '', 'send');
$yform->setHiddenField('customer_id', $user_id);
$yform->setActionField('callback', ['\FriendsOfREDAXO\Simpleshop\CustomerAddress::action__save_checkout_address']);

\rex_extension::register('simpleshop.CustomerAddress.verifyAddressValues', function ($params) {
    global $yform;

    $success = $params->getSubject();
    $values  = $params->getParam('values');

    if ($values['email']['customer_address.1.country'] == 'IT' && trim($values['email']['customer_address.1.fiscal_code']) == '') {
        $success    = false;
        $warnings   = $yform->getObjectparams('warning_messages');
        $warnings[] = strtr(\Sprog\Wildcard::get('error.field_empty'), ['{{fieldname}}' => '###label.fiscal_code###']);
        $yform->setObjectparams('warning_messages', $warnings);
    }
    return $success;
});

echo $yform->getForm();