<?php

/**
 * This file is part of the Kreatif\Project package.
 *
 * @author Kreatif GmbH
 * @author a.platter@kreatif.it
 * Date: 08.08.18
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace FriendsOfREDAXO\Simpleshop;

use Kreatif\Form;


$showSuccess = false;
$customer    = Customer::getCurrentUser();
$address     = $customer->getInvoiceAddress();
$form        = Form::factory('shop-account-data', false);
$form->setObjectparams('fields_class', 'grid-x grid-margin-x');
$form->setObjectparams('getdata', true);
$form->setObjectparams('main_table', Customer::TABLE);
$form->setObjectparams('main_where', "id = {$customer->getId()}");

{
    $field = Customer::getYformFieldByName('email');
    $form->setValueField('email', [
        'name'      => $field->getName(),
        'label'     => $field->getLabel(),
        'css_class' => 'cell medium-6',
        'required'  => true,
    ]);
    $form->setValidateField('empty', [
        'name'    => $field->getName(),
        'message' => str_replace('{{fieldname}}', $field->getLabel(), \Wildcard::get('error.field.empty')),
    ]);
    $form->setValidateField('email', [
        'name'    => $field->getName(),
        'message' => '###error.email_not_valid###',
    ]);
    $form->setValidateField('unique', [
        'name'    => $field->getName(),
        'table'   => Customer::TABLE,
        'message' => '###error.email_already_exists###',
    ]);
}
{
    $form->setValueField('email', [
        'name'      => 'email2',
        'label'     => '###label.repeat_email###',
        'css_class' => 'cell medium-6',
        'required'  => false,
    ]);
    $form->setValidateField('compare', [
        'name'    => 'email2',
        'name2'   => 'email',
        'message' => '###error.email_not_identical_to_repeat###',
    ]);
}
{
    $field = Customer::getYformFieldByName('password');
    $form->setValueField('text', [
        'type'      => 'password',
        'name'      => $field->getName(),
        'label'     => '###label.new_password###',
        'css_class' => 'cell medium-6',
        'required'  => false,
    ]);
    $form->setValidateField('password_policy', [
        'name'    => $field->getName(),
        'message' => '###error.password_policy###',
        'rules'   => '{"length":{"min":8},"letter":{"min":1},"digit":{"min":1}}',
    ]);
    $form->setValidateField('compare', [
        'name'    => $field->getName(),
        'name2'   => 'password2',
        'message' => '###error.new_password_not_identical_to_repeat###',
    ]);
}
{
    $form->setValueField('text', [
        'type'      => 'password',
        'name'      => 'password2',
        'label'     => '###label.repeat_password###',
        'css_class' => 'cell medium-6',
        'required'  => false,
    ]);
}

$form->setValueField('html', ['', '<div class="checkout-buttons cell small-12">']);
{
    $form->setValueField('html', ['', '<button type="submit" class="button">###action.save###</button>']);
}
$form->setValueField('html', ['', '</div>']);

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$formOutput = $form->getForm();
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
if ($form->isSend() && !$form->hasWarnings()) {
    $values = $form->getFormEmailValues();

    foreach ($values as $key => $value) {
        $customer->setValue($key, $value);
    }
    if ($customer->save()) {
        $showSuccess = true;
    } else {
        $formOutput = $form->regenerateForm($customer->getMessages());
    }
}

?>
<div class="customer-area-address">
    <h2 class="margin-small-bottom">
        ###label.auth_data###
    </h2>
    <?php if ($showSuccess): ?>
        <div class="callout success">
            ###label.saved###
        </div>
    <?php endif; ?>
    <?= $formOutput ?>


    <h2 class="margin-small-bottom margin-top">
        ###label.invoice_address###
    </h2>
    <?php
    $fragment = new \rex_fragment();
    $fragment->setVar('Address', $address);
    $fragment->setVar('apply_btn_label', '###action.save###');
    echo $fragment->parse('simpleshop/customer/customer_area/invoice_address.php');
    ?>
</div>
