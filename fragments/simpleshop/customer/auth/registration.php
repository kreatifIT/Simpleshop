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


$sid    = \rex_article::getCurrentId();
$formId = 'shop-registration' . $sid;


// Options
$form = Form::factory($formId, false);
$form->setObjectparams('data_pjax', false);
$form->setObjectparams('fields_class', 'grid-x grid-margin-x');
{
    $field = CustomerAddress::getYformFieldByName('ctype');
    $form->setValueField('choice', [
        'name'       => $field->getName(),
        'label'      => $field->getLabel(),
        'choices'    => $field->getElement('choices'),
        'css_class'  => 'cell medium-6',
        'required'   => true,
        'attributes' => '{"onchange":"Simpleshop.changeCType(this)","data-init-form-toggle":"1"}',
    ]);
    $form->setValidateField('empty', [
        'name'    => $field->getName(),
        'message' => str_replace('{{fieldname}}', $field->getLabel(), \Wildcard::get('error.field.empty')),
    ]);
}
{
    $field = CustomerAddress::getYformFieldByName('company_name');
    $form->setValueField('text', [
        'name'       => $field->getName(),
        'label'      => $field->getLabel(),
        'css_class'  => 'cell medium-6',
        'required'   => true,
        'attributes' => '{"data-form-toggle":"company"}',
    ]);
    $form->setValidateField('customfunction', [
        'name'     => $field->getName(),
        'function' => '\FriendsOfREDAXO\Simpleshop\Customer::customValidateField',
        'params'   => '{"method":"empty-if","dependencies":[{"field":"ctype","value":"company"}]}',
        'message'  => str_replace('{{fieldname}}', $field->getLabel(), \Wildcard::get('error.field.empty')),
    ]);
}
{
    $field = CustomerAddress::getYformFieldByName('firstname');
    $form->setValueField('text', [
        'name'       => $field->getName(),
        'label'      => $field->getLabel(),
        'css_class'  => 'cell medium-6',
        'required'   => true,
        'attributes' => '{"data-form-toggle":"person"}',
    ]);
    $form->setValidateField('customfunction', [
        'name'     => $field->getName(),
        'function' => '\FriendsOfREDAXO\Simpleshop\Customer::customValidateField',
        'params'   => '{"method":"empty-if","dependencies":[{"field":"ctype","value":"person"}]}',
        'message'  => str_replace('{{fieldname}}', $field->getLabel(), \Wildcard::get('error.field.empty')),
    ]);
}
{
    $field = CustomerAddress::getYformFieldByName('lastname');
    $form->setValueField('text', [
        'name'       => $field->getName(),
        'label'      => $field->getLabel(),
        'css_class'  => 'cell medium-6',
        'required'   => true,
        'attributes' => '{"data-form-toggle":"person"}',
    ]);
    $form->setValidateField('customfunction', [
        'name'     => $field->getName(),
        'function' => '\FriendsOfREDAXO\Simpleshop\Customer::customValidateField',
        'params'   => '{"method":"empty-if","dependencies":[{"field":"ctype","value":"person"}]}',
        'message'  => str_replace('{{fieldname}}', $field->getLabel(), \Wildcard::get('error.field.empty')),
    ]);
}
{
    $form->setValueField('html', ['', '<div class="show-for-medium cell medium-6" data-form-toggle="person"></div>']);
}
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
    $form->setValidateField('compare', [
        'name'    => $field->getName(),
        'name2'   => 'email2',
        'message' => '###error.email_not_identical_to_repeat###',
    ]);
}
{
    $form->setValueField('email', [
        'name'      => 'email2',
        'label'     => '###label.repeat_email###',
        'css_class' => 'cell medium-6',
        'required'  => true,
    ]);
}
{
    $field = Customer::getYformFieldByName('password');
    $form->setValueField('text', [
        'type'      => 'password',
        'name'      => $field->getName(),
        'label'     => $field->getLabel(),
        'css_class' => 'cell medium-6',
        'required'  => true,
    ]);
    $form->setValidateField('empty', [
        'name'    => $field->getName(),
        'message' => str_replace('{{fieldname}}', $field->getLabel(), \Wildcard::get('error.field.empty')),
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
        'required'  => true,
    ]);
}
$form->setValueField('html', ['', '<div class="cell">']);
{
    $form->setValueField('submit', [
        'name'        => 'submit',
        'no_db'       => 'no_db',
        'labels'      => ucfirst(\Wildcard::get('action.register')),
        'css_classes' => 'button expanded margin-small-bottom',
    ]);
}
$form->setValueField('html', ['', '</div>']);

$formOutput = $form->getForm();


if ($form->isSend() && !$form->hasWarnings()) {
    $customer = null;
    $values   = $form->getFormEmailValues();

    // register customer
    try {
        $customer = Customer::register($values['email'], $values['password'], $values);
    } catch (CustomerException $ex) {
        $formOutput = $form->regenerateForm(explode('||', $ex->getMessage()));
    } catch (\Exception $ex) {
        $formOutput = $form->regenerateForm([$ex->getMessage()]);
    }
    if ($customer && $customer->getId()) {
        Customer::login($values['email'], $values['password']);

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
    }
}

?>
<?php if ($form->isSend() && !$form->hasWarnings()): ?>
    <div id="<?= $formId ?>">
        <div class="callout success">
            ###label.activation_link_sent###
        </div>
    </div>
<?php else: ?>
    <?= $formOutput ?>
<?php endif; ?>
