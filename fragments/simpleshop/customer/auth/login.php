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
$formId = 'shop-login' . $sid;


$form = Form::factory($formId, false);
$form->setObjectparams('form_action', rex_getUrl(null, null, ['action' => 'login']));
$form->setObjectparams('data_pjax', true);
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
    $field = Customer::getYformFieldByName('password');
    $form->setValueField('text', [
        'name'        => $field->getName(),
        'type'        => 'password',
        'label'       => '',
        'placeholder' => $field->getLabel(),
        'css_class'   => 'cell medium-6',
    ]);
    $form->setValidateField('empty', [
        'name'    => $field->getName(),
        'message' => str_replace('{{fieldname}}', $field->getLabel(), \Wildcard::get('error.field.empty')),
    ]);
}
{
    $form->setValueField('submit', [
        'name'        => 'submit',
        'no_db'       => 'no_db',
        'labels'      => ucfirst(\Wildcard::get('action.login')),
        'css_classes' => 'button expanded margin-bottom',
    ]);
}
$formOutput = $form->getForm();

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($form->isSend() && !$form->hasWarnings()) {
    $values = $form->getFormEmailValues();

    if (Customer::login($values['email'], $values['password'])) {
        $redirectUrl = '';
        $referer     = rex_session('login_referer', 'string');
        rex_unset_session('login_referer');

        if ($referer == 'account') {
            $article     = Settings::getArticle('dashboard');
            $redirectUrl = $article ? $article->getUrl() : '';
        }
        if ($redirectUrl == '' && strlen($referer)) {
            $redirectUrl = $referer;
        }
        if ($redirectUrl == '') {
            unset($_GET['action']);
            $redirectUrl = rex_getUrl(null, null, $_GET);
        }
        if (\rex_request::isXmlHttpRequest()) {
            \rex_response::cleanOutputBuffers();
            \rex_response::sendJson(['redirectUrl' => $redirectUrl]);
        } else {
            \rex_response::sendCacheControl();
            \rex_response::sendRedirect($redirectUrl);
        }
        exit;
    } else {
        $formOutput = $form->regenerateForm(['###error.login_failed###']);
    }
}

?>
<?= $formOutput ?>
