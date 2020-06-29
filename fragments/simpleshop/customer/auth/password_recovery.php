<?php

/**
 * This file is part of the Kreatif\Project package.
 *
 * @author Kreatif GmbH
 * @author a.platter@kreatif.it
 * Date: 26.06.20
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FriendsOfREDAXO\Simpleshop;

use Kreatif\Form;


$sid    = \rex_article::getCurrentId();
$formId = 'shop-recovery' . $sid;

$form = Form::factory($formId, false);
$form->setObjectparams('data_pjax', true);
$form->setObjectparams('form_action', rex_getUrl(null, null, ['action' => 'recover']));

    {
        $field = Customer::getYformFieldByName('email');
        $form->setValueField('text', [
            'name'        => $field->getName(),
            'label'       => '',
            'placeholder' => $field->getLabel(),
            'css_class'   => 'cell medium-6',
        ]);
        $form->setValidateField('empty', [
            'name'    => $field->getName(),
            'message' => str_replace('{{fieldname}}', $field->getLabel(), \Wildcard::get('error.field.empty')),
        ]);
        $form->setValidateField('email', [
            'name'    => $field->getName(),
            'message' => '###error.email_not_valid###',
        ]);
    }
    {
        $form->setValueField('submit', [
            'name'        => 'submit',
            'no_db'       => 'no_db',
            'labels'      => ucfirst(\Wildcard::get('action.reset')),
            'css_classes' => 'button expanded margin-bottom',
        ]);
    }
$formOutput = $form->getForm();

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($form->isSend() && !$form->hasWarnings()) {
    $values = $form->getFormEmailValues();
    Customer::resetPassword($values['email']);
} else {
    echo $formOutput;
}

?>
<?php if ($form->isSend() && !$form->hasWarnings()): ?>
    <div id="<?= $formId ?>">
        <div class="callout success">
            ###notif.password_reset_msg###
        </div>
    </div>
<?php endif; ?>