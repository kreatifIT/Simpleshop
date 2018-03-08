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

$_config = $this->getVar('config', []);
$Config  = array_merge([
    'has_registration'      => true,
    'has_password_recovery' => true,
], $_config);

$Config['css_class'] = array_merge([
    'wrapper' => 'margin-large-top margin-large-bottom',
    'buttons' => 'expanded margin-bottom',
], (array) $_config['css_class']);


$errors   = [];
$id       = 'auth-' . \rex_article::getCurrentId();
$sid      = "form-{$id}";
$action   = rex_request('action', 'string');
$Settings = \rex::getConfig('simpleshop.Settings');


if (rex_post('action', 'string') == 'login') {
    if (Customer::login(rex_post('uname', 'string'), rex_post('pwd', 'string'))) {
        $referer = rex_session('login_referer', 'string');
        rex_unset_session('login_referer');

        if (strlen($referer)) {
            header('Location: ' . $referer);
            exit;
        }
        rex_redirect($Settings['linklist']['dashboard']);
    }
    else {
        $errors[] = '###error.login_failed###';
    }
}
if (rex_get('return', 'int') == 1) {
    rex_set_session('login_referer', rex_server('HTTP_REFERER'));
}


?>
<div id="<?= $sid ?>" class="auth-wrapper <?= $Config['css_class']['wrapper'] ?>">
    <div class="login-form <?= $action == 'recover' || $action == 'register' ? 'hide' : '' ?>">
        <div class="row column">
            <form action="<?= rex_getUrl($Settings['linklist']['dashboard']) ?>#-<?= $sid ?>" method="post" class="padding-small-top padding-small-bottom">
                <h2 class="margin-small-bottom heading"><?= ucfirst(\Sprog\Wildcard::get('action.login')) ?></h2>

                <?php if (count($errors)): ?>
                    <div class="callout alert">
                        <?= implode('<br/>', $errors) ?>
                    </div>
                <?php endif; ?>

                <div class="input-group">
                    <input type="text" class="input-group-field" name="uname" value="<?= rex_post('uname', 'string') ?>" placeholder="###label.email###" tabindex="103"/>
                </div>
                <div class="input-group">
                    <input type="password" class="input-group-field" name="pwd" value="" placeholder="###label.password###" tabindex="104"/>
                </div>
                <button type="submit" class="button <?= $Config['css_class']['buttons'] ?>" tabindex="105" name="action" value="login"><?= ucfirst(\Sprog\Wildcard::get('action.login')) ?></button>

                <?php if ($Config['has_password_recovery']): ?>
                    <a href="javascript:;" onclick="Simpleshop.toggleAuth(this, '.recovery-form')">###action.reset_password###.</a>
                <?php endif; ?>
                <?php if ($Config['has_registration']): ?>
                    ###label.no_login_data###: <a href="javascript:;" onclick="Simpleshop.toggleAuth(this, '.register-form')">###label.register_now###</a>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <?php if ($Config['has_password_recovery']): ?>
        <?php
        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        // PASSWORD RECOVERY
        ?>
        <div class="recovery-form <?= $action == 'recover' ? '' : 'hide' ?>">
            <div class="row column">
                <h2 class="margin-small-bottom heading"><?= ucfirst(\Sprog\Wildcard::get('label.password_forgotten')) ?></h2>

                <?php
                $recoverySuccess = false;

                if (rex_post('action', 'string') == 'recover') {
                    $recoverySuccess = true;
                    $email           = rex_post('uname', 'string');
                    Customer::resetPassword($email);
                }

                ?>
                <?php if ($recoverySuccess): ?>
                    <div class="callout success">###notif.password_reset_msg###</div>
                <?php else: ?>
                    <form action="<?= rex_getUrl($Settings['linklist']['dashboard']) ?>#-<?= $sid ?>" method="post">
                        <input type="text" name="uname" placeholder="###label.email###" value="<?= rex_post('uname', 'string'); ?>">
                        <button type="submit" class="button <?= $Config['css_class']['buttons'] ?>" name="action" value="recover">
                            ###action.reset###
                        </button>
                    </form>
                <?php endif ?>
                <a href="javascript:;" onclick="Simpleshop.toggleAuth(this, '.login-form')">###action.back_to_login###</a>.
                <?php if ($Config['has_registration']): ?>
                    ###label.no_login_data###: <a href="javascript:;" onclick="Simpleshop.toggleAuth(this, '.register-form')">###label.register_now###</a>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>




    <?php if ($Config['has_registration']): ?>
        <?php
        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        // REGISTRATION
        $excludesFields = ['lang_id', 'addresses', 'status', 'lastlogin', 'created'];
        $fields         = Customer::getAllYformFields();
        $form           = \Kreatif\Form::factory();

        // Options
        $form->setObjectparams('form_anchor', '-' . $sid);
        $form->setObjectparams('submit_btn_show', false);
        $form->setObjectparams('real_field_names', false);
        $form->setObjectparams('form_ytemplate', 'custom,foundation,bootstrap');
        $form->setObjectparams('error_class', 'form-warning');
        $form->setObjectparams('form_showformafterupdate', true);


        foreach ($fields as $index => $field) {
            // exclude types
            if ($field->getElement('name') == 'hidden_fields' || in_array($field->getElement('name'), $excludesFields)) {
                continue;
            }

            if ($field->getElement('type_id') == 'value') {
                $params = array_merge($field->toArray(), [
                    'css_class' => 'column small-12',
                ]);

                if ($field->getElement('type_name') == 'be_manager_relation') {
                    $params['field'] = strtr($params['field'], ['_1' => '_' . \rex_clang::getCurrentId()]);
                }
                $form->setValueField($field->getElement('type_name'), $params);
            }
            else if ($field->getElement('type_id') == 'validate') {
                $form->setValidateField($field->getElement('type_name'), $field->toArray());
            }
        }


        // Submit
        $form->setValueField('html', ['', '<div class="column margin-small-top">']);
        $form->setValueField('submit', [
            'name'        => 'submit',
            'no_db'       => 'no_db',
            'labels'      => strtoupper(\Wildcard::get('action.save')),
            'css_classes' => 'button ' . $Config['css_class']['buttons'],
        ]);
        $form->setValueField('html', ['', '</div>']);
        $form->setValueField('html', ['', '<input type="hidden" name="action" value="register">']);

        $customerError = false;
        $formOutput    = $form->getForm();
        $values        = $form->getFormEmailValues();

        // register customer
        try {
            Customer::register($values['email'], $values['password'], $values);
            Customer::login($values['email'], $values['password']);
        }
        catch (CustomerException $ex) {
            $customerError = true;
            $formOutput    = '<div class="callout alert">' . $ex->getMessage() . '</div>' . $formOutput;
        }

        ?>
        <div class="register-form <?= $action == 'register' ? '' : 'hide' ?>">
            <div class="row column">
                <h2 class="margin-small-bottom heading"><?= ucfirst(\Sprog\Wildcard::get('action.register')) ?></h2>
            </div>

            <div class="row">
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
                    <div class="column">
                        <a href="javascript:;" onclick="Simpleshop.toggleAuth(this, '.login-form')">###action.back_to_login###</a>.
                    </div>
                <?php endif; ?>

            </div>

        </div>
    <?php endif; ?>
</div>
