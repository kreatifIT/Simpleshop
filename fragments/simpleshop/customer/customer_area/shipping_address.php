<?php

/**
 * This file is part of the Kreatif\Project package.
 *
 * @author Kreatif GmbH
 * @author a.platter@kreatif.it
 * Date: 24.06.20
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FriendsOfREDAXO\Simpleshop;

use Kreatif\Form;
use Kreatif\Model\Country;


$backUrl       = trim($this->getVar('back_url', ''));
$address       = $this->getVar('Address');
$applyBtnLabel = $this->getVar('apply_btn_label', '###action.save###');
$saveCallback  = $this->getVar('save_callback', null);


$showSuccess = false;
$sql         = \rex_sql::factory();
$clangId     = \rex_clang::getCurrentId();
$customer    = Customer::getCurrentUser();
$isCompany   = $address ? $address->isCompany() : false;
$form        = Form::factory('shop-shipping-address', false);
$form->setObjectparams('fields_class', 'grid-x grid-margin-x');

if ($address && $address->getId() > 0) {
    $form->setObjectparams('getdata', true);
    $form->setObjectparams('main_table', CustomerAddress::TABLE);
    $form->setObjectparams('main_where', "id = {$address->getId()}");
}
if ($isCompany) {
    $field = CustomerAddress::getYformFieldByName('company_name');
    $form->setValueField('text', [
        'name'      => $field->getName(),
        'label'     => $field->getLabel(),
        'css_class' => 'cell medium-6',
        'required'  => true,
    ]);
    $form->setValidateField('empty', [
        'name'    => $field->getName(),
        'message' => str_replace('{{fieldname}}', $field->getLabel(), \Wildcard::get('error.field.empty')),
    ]);
}
if (!$isCompany) {
    $field = CustomerAddress::getYformFieldByName('firstname');
    $form->setValueField('text', [
        'name'      => $field->getName(),
        'label'     => $field->getLabel(),
        'css_class' => 'cell medium-6',
        'required'  => true,
    ]);
    $form->setValidateField('empty', [
        'name'    => $field->getName(),
        'message' => str_replace('{{fieldname}}', $field->getLabel(), \Wildcard::get('error.field.empty')),
    ]);
}
if (!$isCompany) {
    $field = CustomerAddress::getYformFieldByName('lastname');
    $form->setValueField('text', [
        'name'      => $field->getName(),
        'label'     => $field->getLabel(),
        'css_class' => 'cell medium-6',
        'required'  => true,
    ]);
    $form->setValidateField('empty', [
        'name'    => $field->getName(),
        'message' => str_replace('{{fieldname}}', $field->getLabel(), \Wildcard::get('error.field.empty')),
    ]);
}
{
    $field = CustomerAddress::getYformFieldByName('street');
    $form->setValueField('text', [
        'name'      => $field->getName(),
        'label'     => $field->getLabel(),
        'required'  => true,
        'css_class' => 'cell medium-6',
    ]);
    $form->setValidateField('empty', [
        'name'    => $field->getName(),
        'message' => str_replace('{{fieldname}}', $field->getLabel(), \Wildcard::get('error.field.empty')),
    ]);
}
{
    $field = CustomerAddress::getYformFieldByName('street_additional');
    $form->setValueField('text', [
        'name'      => $field->getName(),
        'label'     => $field->getLabel(),
        'required'  => false,
        'css_class' => 'cell medium-6',
    ]);
}
{
    $field = CustomerAddress::getYformFieldByName('location');
    $form->setValueField('text', [
        'name'      => $field->getName(),
        'label'     => $field->getLabel(),
        'required'  => true,
        'css_class' => 'cell medium-6',
    ]);
    $form->setValidateField('empty', [
        'name'    => $field->getName(),
        'message' => str_replace('{{fieldname}}', $field->getLabel(), \Wildcard::get('error.field.empty')),
    ]);
}
{
    $field = CustomerAddress::getYformFieldByName('postal');
    $form->setValueField('text', [
        'name'      => $field->getName(),
        'label'     => $field->getLabel(),
        'required'  => true,
        'css_class' => 'cell medium-6',
    ]);
    $form->setValidateField('empty', [
        'name'    => $field->getName(),
        'message' => str_replace('{{fieldname}}', $field->getLabel(), \Wildcard::get('error.field.empty')),
    ]);
}
{
    $countries = $sql->getArray("SELECT id, name_{$clangId} AS label FROM " . Country::TABLE . ' WHERE status = 1', [], \PDO::FETCH_ASSOC);
    $list      = new \rex_yform_choice_list([]);
    $list->createListFromSqlArray($countries);
    $choices = $list->getChoicesByValues();
    $field   = CustomerAddress::getYformFieldByName('country');
    $form->setValueField('choice', [
        'name'        => $field->getName(),
        'label'       => $field->getLabel(),
        'choices'     => $choices,
        'required'    => true,
        'css_class'   => 'cell medium-6',
        'placeholder' => '- ###action.select.country### -',
    ]);
    $form->setValidateField('empty', [
        'name'    => $field->getName(),
        'message' => str_replace('{{fieldname}}', $field->getLabel(), \Wildcard::get('error.field.empty')),
    ]);
}
{
    $form->setValueField('hidden', ['customer_id', $customer ? $customer->getId() : null, '', false]);
}

$form->setValueField('html', ['', '<div class="checkout-buttons cell small-12">']);
{
    if ($backUrl != '') {
        $form->setValueField('html', ['', '<a href="'. $backUrl .'" class="button hollow">###action.go_back###</a>']);
    }
    $form->setValueField('html', ['', '<button type="submit" class="button">' . $applyBtnLabel . '</button>']);
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
        $address->setValue($key, $value);
    }
    if ($address->save()) {
        if (is_callable($saveCallback)) {
            $saveCallback($address);
        }
        $showSuccess = true;
    }
    else {
        $formOutput = $form->regenerateForm($address->getMessages());
    }
}
?>
    <?php if ($showSuccess): ?>
    <div class="callout success">
        ###label.saved###
    </div>
<?php endif; ?>

<?= $formOutput ?>