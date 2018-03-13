<?php

/**
 * This file is part of the Kreatif\Project package.
 *
 * @author Kreatif GmbH
 * @author a.platter@kreatif.it
 * Date: 09.03.18
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace FriendsOfREDAXO\Simpleshop;

$type          = $this->getVar('form_field_type', 'account');
$show_save_btn = $this->getVar('show_save_btn', true);
$fields        = $this->getVar('additional_fields', []);
$Customer      = Customer::getCurrentUser();


$id   = 'account-data-' . \rex_article::getCurrentId();
$sid  = "form-{$id}";
$form = Customer::getAccountFieldForm(\Kreatif\Form::factory(), $Customer, $type, $fields);
$form->setObjectparams('form_anchor', '-' . $sid);
$form->setObjectparams('form_name', $sid);
$form->setValueField('html', ['', '<input type="hidden" name="yform-id" value="' . $sid . '">']);

if ($show_save_btn) {
    // Submit
    $form->setValueField('html', ['', '<div class="column margin-small-top">']);
    $form->setValueField('submit', [
        'name'        => 'submit-' . $sid,
        'no_db'       => 'no_db',
        'labels'      => strtoupper(\Wildcard::get('action.save')),
        'css_classes' => 'button',
    ]);
    $form->setValueField('html', ['', '</div>']);
    $form->setValueField('html', ['', '<input type="hidden" name="action" value="save-account">']);
}

$formOutput = $form->getForm();

?>
<div class="account-data margin-small-top margin-bottom">
    <div class="row">

        <?= $formOutput ?>

    </div>
</div>
