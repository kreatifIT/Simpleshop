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


use Kreatif\Form;


$back_url         = $this->getVar('back_url', '');
$show_save_btn    = $this->getVar('show_save_btn', true);
$real_field_names = $this->getVar('real_field_names', false);
$only_fields      = $this->getVar('only_fields', false);
$btn_label        = $this->getVar('btn_label', ucfirst(\Wildcard::get('action.save')));
$Customer         = $this->getVar('Customer', Customer::getCurrentUser());


$id  = 'form-data-' . \rex_article::getCurrentId();
$sid = "form-{$id}";


$form = Form::factory();
$form->setObjectparams('submit_btn_show', false);
$form->setObjectparams('real_field_names', $real_field_names);
$form->setObjectparams('form_ytemplate', 'custom,foundation,bootstrap');
$form->setObjectparams('error_class', 'form-warning');
$form->setObjectparams('form_showformafterupdate', true);
$form->setObjectparams('getdata', $Customer->getId() > 0);
$form->setObjectparams('main_table', Customer::TABLE);
$form->setObjectparams('main_where', "id = {$Customer->getId()}");
$form->setObjectparams('debug', false);
$form->setObjectparams('form_anchor', '-' . $sid);
$form->setObjectparams('form_name', $sid);
$form->setObjectparams('form_class', 'grid-x grid-margin-x');
$form->setObjectparams('form_action', '');
$form->setObjectparams('only_fields', $only_fields);


$field = Customer::getYformFieldByName('client_id');
$form->setValueField('integer', [
    'name'     => $field->getName(),
    'label'    => $field->getLabel(),
    'readonly' => true,
]);

$field = Customer::getYformFieldByName('email');
$form->setValueField('email', [
    'name'  => $field->getName(),
    'label' => $field->getLabel(),
]);

$field = Customer::getYformFieldByName('password');
$form->setValueField('text', [
    'name'  => $field->getName(),
    'label' => $field->getLabel() . ' (' . $field->getElement('notice') . ')',
    'type'  => $field->getLabel(),
]);

if ($show_save_btn) {
    // Submit
    $form->setValueField('html', ['', '<div class="cell margin-small-top">']);

    if ($back_url) {
        $form->setValueField('html', ['', '<a href="' . $back_url . '" class="button">###action.go_back###</a>']);
    }

    // Submit
    $form->setValueField('submit', [
        'name'        => 'submit-' . $sid,
        'no_db'       => 'no_db',
        'labels'      => $btn_label,
        'css_classes' => 'button',
    ]);
    $form->setValueField('html', ['', '</div>']);
}

$formOutput = $form->getForm();
$errors     = [];

if ($form->isSend() && !$form->hasWarnings()) {
    $values = $form->getFormEmailValues();

    foreach ($values as $key => $value) {
        $Customer->setValue($key, $value);
    }
    if (!$Customer->save()) {
        $errors = $Customer->getMessages();
    }
}

?>
<div class="account-data margin-small-top margin-bottom">
    <?php if (count($errors)): ?>
        <div class="callout alert">
            <?= implode('<br/>', $errors) ?>
        </div>
    <?php elseif ($form->isSend() && !$form->hasWarnings()): ?>
        <div class="callout success">
            ###label.save_successfull###
        </div>
    <?php endif; ?>
    <?= $formOutput ?>
</div>