<?php

/**
 * This file is part of the Kreatif\Project package.
 *
 * @author Kreatif GmbH
 * @author a.platter@kreatif.it
 * Date: 02.03.20
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace FriendsOfREDAXO\Simpleshop;

use Kreatif\Form;


$sid        = 'form-auth-' . \rex_article::getCurrentId();
$action     = trim(rex_post('action', 'string'));
$form       = Form::factory();
$formFields = CustomerAddress::getAllYformFields();
$formFields = array_merge($formFields, Customer::getAllYformFields());

$Config    = FragmentConfig::getValue('yform_fields.rex_shop_customer');
$excFields = FragmentConfig::getValue('yform_fields.rex_shop_customer._excludedFields');
$excFields = array_merge($excFields, FragmentConfig::getValue('yform_fields.rex_shop_customer_address._excludedFields'));
$excFields = array_merge($excFields, FragmentConfig::getValue('auth.registration_excl_fields'));

FragmentConfig::$data['yform_fields']['rex_shop_customer']['_excludedFields'] = $excFields;

// Options
$form->setObjectparams('form_anchor', '-' . $sid);
$form->setObjectparams('submit_btn_show', false);
$form->setObjectparams('real_field_names', true);
$form->setObjectparams('form_ytemplate', 'custom,foundation,bootstrap');
$form->setObjectparams('error_class', 'form-warning');
$form->setObjectparams('form_showformafterupdate', true);
$form->setObjectparams('fields_class', 'grid-x grid-margin-x');


foreach ($formFields as $field) {
    // exclude types
    if (in_array($field->getElement('name'), $excFields)) {
        continue;
    }

    $fieldType = $field->getElement('type_id');

    if ($fieldType == 'validate') {
        // set validate field
        $form->setValidateField($field->getElement('type_name'), $field->toArray());
    } else if ($fieldType == 'value') {
        // set value field
        $params              = array_merge($field->toArray(), (array)$Config[$field->getElement('name')], [
            'notice' => null,
        ]);
        $params['css_class'] .= ' cell medium-6';

        if ($field->getElement('type_name') == 'be_manager_relation') {
            $params['just_names'] = true;
            $params['field']      = strtr($params['field'], ['_1' => '_' . \rex_clang::getCurrentId()]);
        }
        $form->setValueField($field->getElement('type_name'), $params);
    }
}


// validate empty password
$form->setValidateField('empty', [
    'type_id'   => 'validate',
    'type_name' => 'empty',
    'name'      => 'password',
    'message'   => '###error.password_policy###',
]);


// Submit
$form->setValueField('html', ['', '<div class="margin-small-top cell">']);
$form->setValueField('submit', [
    'name'        => 'submit',
    'no_db'       => 'no_db',
    'labels'      => strtoupper(\Wildcard::get('action.register')),
    'css_classes' => 'button expanded ' . $Config['css_class']['buttons'],
]);
$form->setValueField('html', ['', '</div>']);
$form->setValueField('html', ['', '<input type="hidden" name="action" value="register">']);

$customerError = false;
$formOutput    = $form->getForm();

if ($action == 'register' && $form->isSend() && !$form->hasWarnings()) {
    $values = $form->getFormEmailValues();

    // register customer
    try {
        Customer::register($values['email'], $values['password'], $values);
        Customer::login($values['email'], $values['password']);
    } catch (CustomerException $ex) {
        $customerError = true;
        $formOutput    = '<div class="callout alert">' . $ex->getMessage() . '</div>' . $formOutput;
    }
}

?>
<?php if ($form->isSend() && !$form->hasWarnings() && !$customerError):
    $referer = rex_session('login_referer', 'string');
    rex_unset_session('login_referer');

    if (strlen($referer)) {
        header('Location: ' . $referer);
        exit;
    }
    ?>
    <div class="callout success">###label.registration_sucessfull###</div>
<?php else: ?>
    <?= $formOutput ?>
<?php endif; ?>
