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

use Sprog\Wildcard;

$User = $this->getVar('user');
$name = trim($User->getValue('firstname') . " " . $User->getValue('lastname'));

?>
    <p class="margin-bottom"><?= strtr(Wildcard::get('shop.my_account_overview_text'), ['{{name}}' => '<strong>' . $name . '</strong>']) ?></p>

    <h2>###shop.account_data###</h2>
    <?php


$User = Customer::getCurrentUser();
$User->addExcludedField(['status', 'lastlogin', 'created', 'addresses']);
$User->setFieldData('email_validate_empty', ['message' => strtr(Wildcard::get('error.field_empty'), ['{{fieldname}}' => 'Email'])]);
$User->setFieldData('email_validate_unique', ['message' => '###error.user_already_exist###']);
$User->setFieldData('email_validate_email', ['message' => '###error.email_not_valid###']);
$User->setFieldData('password_value_text', ['notice' => '###shop.password_saving_msg###']);
$User->setFieldData('password_validate_size_range', ['message' => strtr(Wildcard::get('error.password_to_short'), ['%d' => Customer::MIN_PASSWORD_LENGTH])]);

$yform = $User->getForm();
$yform->addTemplatePath(\rex_path::addon('project') . 'templates');
$yform->setObjectparams('getdata', TRUE);
$yform->setObjectparams('form_ytemplate', 'form,bootstrap');
$yform->setObjectparams('error_class', 'form_warning');
$yform->setObjectparams('submit_btn_show', FALSE);
$yform->setObjectparams('form_name', 'account_data');
$yform->setObjectparams('form_action', '');
$yform->setObjectparams('form_class', 'row column');
$yform->setObjectparams('form_showformafterupdate', TRUE);
$yform->setValueField('submit', [
    'name'        => 'submit',
    'labels'      => '###action.save###',
    'no_db'       => TRUE,
    'css_classes' => 'submit-button-container',
]);

echo $yform->getForm();