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

$subject = $_GET['request'];
$form    = \Kreatif\Form::factory();


// Options
$form->setObjectparams('submit_btn_show', FALSE);
$form->setObjectparams('form_skin', 'form,bootstrap');
$form->setObjectparams('error_class', 'row column form_warning');


// Row 1
$form->setValueField('html', ['opening_tag', '<div class="row margin-top">']);

$form->setValueField('text', [
    'name'      => 'subject',
    'default'   => $subject,
    'label'     => '###label.subject###',
    'css_class' => 'medium-4 columns',
    'required'  => 1,
]);
$form->setValidateField('empty', [
    'subject',
    strtr(\Sprog\Wildcard::get('error.field_empty'), ['{{fieldname}}' => '###label.subject###']),
]);

$form->setValueField('text', [
    'name'      => 'fullname',
    'label'     => '###label.fullname###',
    'css_class' => 'medium-4 columns',
    'required'  => 1,
]);
$form->setValidateField('empty', [
    'fullname',
    strtr(\Sprog\Wildcard::get('error.field_empty'), ['{{fieldname}}' => '###label.fullname###']),
]);


$form->setValueField('text', [
    'name'      => 'email',
    'label'     => '###label.email###',
    'css_class' => 'medium-4 columns',
    'required'  => 1,
]);
$form->setValidateField('empty', [
    'email',
    strtr(\Sprog\Wildcard::get('error.field_empty'), ['{{fieldname}}' => '###label.email###']),
]);
$form->setValidateField('email', ['email', \Sprog\Wildcard::get('error.email_not_valid')]);

$form->setValueField('html', ['closing_tag', '</div>']);


// Row 2
$form->setValueField('html', ['opening_tag', '<div class="row margin-top">']);
$form->setValueField('text', [
    'name'      => 'phone',
    'label'     => '###label.phone###',
    'css_class' => 'medium-4 columns',
]);
$form->setValueField('text', [
    'name'      => 'location',
    'label'     => '###label.location###',
    'css_class' => 'medium-4 columns',
]);

$form->setValueField('text', [
    'name'      => 'street',
    'label'     => '###label.street###',
    'css_class' => 'medium-4 columns',
]);

$form->setValueField('html', ['closing_tag', '</div>']);

// Row 3
$form->setValueField('html', ['opening_tag', '<div class="row column margin-top">']);

$form->setValueField('textarea', ['message', '###label.message###', '', FALSE, '', 'required' => 1, 'rows' => 5]);
$form->setValidateField('empty', [
    'message',
    strtr(\Sprog\Wildcard::get('error.field_empty'), ['{{fieldname}}' => '###label.message###']),
]);

$form->setValueField('html', ['closing_tag', '</div>']);

// Terms & Conditions
$form->setValueField('html', ['opening_tag', '<div class="row column">']);
$form->setValueField('checkbox', ['privacy', '###privacy.accept_tos###', 1, 0, FALSE, 'required' => 1]);
$form->setValidateField('empty', [
    'privacy',
    strtr(\Sprog\Wildcard::get('error.checkbox_not_set'), ['{{fieldname}}' => '###privacy.accept_tos###']),
]);
$form->setValueField('html', ['closing_tag', '</div>']);


// Submit
$form->setValueField('html', ['opening_tag', '<div class="row column">']);
$form->setValueField('submit', ['', '###label.submit###', '', 'submit-button-container']);
$form->setValueField('html', ['closing_tag', '</div>']);

echo $form->getForm();

?>