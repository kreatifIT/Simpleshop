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


$referer  = $this->getVar('referer');
$Config   = FragmentConfig::getValue('auth');
$errors   = [];
$id       = 'auth-' . \rex_article::getCurrentId();
$sid      = "form-{$id}";
$action   = rex_request('action', 'string');
$Settings = \rex::getConfig('simpleshop.Settings');

if (rex_post('action', 'string') == 'login') {
    if (Customer::login(rex_post('uname', 'string'), rex_post('pwd', 'string'))) {
        $referer = rex_session('login_referer', 'string');
        rex_unset_session('login_referer');

        unset($_GET['action']);

        switch ($referer) {
            case 'account':
                rex_redirect($Settings['linklist']['dashboard'], null, ['ts' => time()]);
                break;
            default:
                if ($referer == '') {
                    rex_redirect(\rex_article::getCurrentId(), null, array_merge($_GET, ['ts' => time()]));
                } else if (strlen($referer)) {
                    header('Location: ' . $referer);
                    exit;
                }
                break;
        }
    } else {
        $errors[] = '###error.login_failed###';
    }
}
if (rex_get('referer', 'string') == 'return') {
    rex_set_session('login_referer', rex_server('HTTP_REFERER'));
} else if (rex_get('referer', 'string') == 'account') {
    rex_set_session('login_referer', 'account');
} else if (strlen($referer)) {
    rex_set_session('login_referer', $referer);
}

unset($_GET['action']);

$texts = [
    'registration_info_text' => \Wildcard::get('simpleshop.registration_info_text'),
    'login_info_text'        => \Wildcard::get('simpleshop.login_info_text'),
    'pwd_recovery_info_text' => \Wildcard::get('simpleshop.pwd_recovery_info_text'),
];

?>
<div id="<?= $sid ?>" class="auth-wrapper <?= $Config['css_class']['wrapper'] ?>" data-auth-wrapper>
    <div class="login-form <?= $action == 'recover' || $action == 'register' ? 'hide' : '' ?>">
        <div class="row medium-6 large-4 medium-centered">
            <div class="column">

                <form action="<?= rex_getUrl(null, null, $_GET) ?>#-<?= $sid ?>" method="post" class="padding-small-top padding-small-bottom">
                    <h2 class="margin-small-bottom heading"><?= ucfirst(\Sprog\Wildcard::get('action.login')) ?></h2>

                    <?php if (strlen($texts['login_info_text'])): ?>
                        <p><?= $texts['login_info_text'] ?></p>
                    <?php endif; ?>

                    <?php if (count($errors)): ?>
                        <div class="callout alert">
                            <?= implode('<br/>', $errors) ?>
                        </div>
                    <?php endif; ?>

                    <div class="input-group">
                        <input type="email" class="input-group-field" name="uname" value="<?= rex_post('uname', 'string') ?>" placeholder="###label.email###" tabindex="103"/>
                    </div>
                    <div class="input-group">
                        <input type="password" class="input-group-field" name="pwd" value="" placeholder="###label.password###" tabindex="104"/>
                    </div>
                    <button type="submit" class="button <?= $Config['css_class']['buttons'] ?>" tabindex="105" name="action"
                            value="login"><?= ucfirst(\Sprog\Wildcard::get('action.login')) ?></button>

                    <?php if ($Config['has_password_recovery']): ?>
                        <div class="login-form-passwort-reset">
                            <a class="text-link" href="javascript:;" onclick="Simpleshop.toggleAuth(this, '.recovery-form')">###action.reset_password###.</a>
                        </div>
                    <?php endif; ?>
                    <?php if ($Config['has_registration']): ?>
                        <div class="login-form-register">
                            <span class="label">###label.no_login_data###:</span class="login-form-register-label">
                            <a class="text-link" href="javascript:;" onclick="Simpleshop.toggleAuth(this, '.register-form')">###label.register_now###</a>
                        </div>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    </div>

    <?php if ($Config['has_password_recovery']): ?>
        <?php
        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        // PASSWORD RECOVERY
        ?>
        <div class="recovery-form medium-6 large-4 medium-centered <?= $action == 'recover' ? '' : 'hide' ?>">
            <div class="row column">
                <h2 class="margin-small-bottom heading"><?= ucfirst(\Sprog\Wildcard::get('label.password_forgotten')) ?></h2>

                <?php if (strlen($texts['pwd_recovery_info_text'])): ?>
                    <p><?= $texts['pwd_recovery_info_text'] ?></p>
                <?php endif; ?>

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
                    <form action="#-<?= $sid ?>" method="post">
                        <input type="email" name="uname" placeholder="###label.email###" value="<?= rex_post('uname', 'string'); ?>">
                        <button type="submit" class="button <?= $Config['css_class']['buttons'] ?>" name="action" value="recover">
                            ###action.reset###
                        </button>
                    </form>
                <?php endif ?>
                <a href="javascript:;" onclick="Simpleshop.toggleAuth(this, '.login-form')" class="text-link">###action.back_to_login###</a>.
                <?php if ($Config['has_registration']): ?>
                    ###label.no_login_data###: <a href="javascript:;" onclick="Simpleshop.toggleAuth(this, '.register-form')" class="text-link">###label.register_now###</a>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>




    <?php if ($Config['has_registration']): ?>
        <?php
        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        // REGISTRATION
        $fields    = Customer::getAllYformFields();
        $form      = Form::factory();
        $excFields = FragmentConfig::getValue('yform_fields.rex_shop_customer._excludedFields');
        $excFields = array_merge($excFields, FragmentConfig::getValue('auth.registration_excl_fields'));

        FragmentConfig::$data['yform_fields']['rex_shop_customer']['_excludedFields'] = $excFields;

        $Config = FragmentConfig::getValue('yform_fields.rex_shop_customer');

        // Options
        $form->setObjectparams('form_anchor', '-' . $sid);
        $form->setObjectparams('submit_btn_show', false);
        $form->setObjectparams('real_field_names', true);
        $form->setObjectparams('form_ytemplate', 'custom,foundation,bootstrap');
        $form->setObjectparams('error_class', 'form-warning');
        $form->setObjectparams('form_showformafterupdate', true);


        foreach ($fields as $index => $field) {
            // exclude types
            if (in_array($field->getElement('name'), $excFields)) {
                continue;
            }

            if ($field->getElement('type_id') == 'value') {
                $params = array_merge($field->toArray(), (array)$Config[$field->getElement('name')], [
                    'notice' => null,
                ]);

                $params['css_class'] .= ' column medium-6';

                if ($field->getElement('type_name') == 'be_manager_relation') {
                    $params['field'] = strtr($params['field'], ['_1' => '_' . \rex_clang::getCurrentId()]);
                }
                $form->setValueField($field->getElement('type_name'), $params);
            } else if ($field->getElement('type_id') == 'validate') {
                $form->setValidateField($field->getElement('type_name'), $field->toArray());
            }
        }


        // validate empty password
        $form->setValidateField('empty', [
            'type_id'   => 'validate',
            'type_name' => 'empty',
            'name'      => 'password',
            'message'   => '###error.password_policy###',
        ]);


        // Submit
        $form->setValueField('html', ['', '<div class="column margin-small-top">']);
        $form->setValueField('submit', [
            'name'        => 'submit',
            'no_db'       => 'no_db',
            'labels'      => strtoupper(\Wildcard::get('action.register')),
            'css_classes' => 'button expanded ' . $Config['css_class']['buttons'],
        ]);
        $form->setValueField('html', ['', '</div>']);
        $form->setValueField('html', ['', '<input type="hidden" name="action" value="register">']);

        $customerError = false;
        $formOutput    = $form->getForm();

        if (rex_post('action', 'string') == 'register' && $form->isSend() && !$form->hasWarnings()) {
            $values = $form->getFormEmailValues();

            // register customer
            try {
                Customer::register($values['email'], $values['password'], $values);
                Customer::login($values['email'], $values['password']);
            } catch (CustomerException $ex) {
                $customerError = true;
                $formOutput    = '<div class="callout alert">' . $ex->getMessage() . '</div>' . $formOutput;
            }
        }

        ?>
        <div class="register-form large-6 medium-centered <?= $action == 'register' ? '' : 'hide' ?>">
            <div class="row column">
                <h2 class="margin-small-bottom heading"><?= ucfirst(\Sprog\Wildcard::get('action.register')) ?></h2>

                <?php if (strlen($texts['registration_info_text'])): ?>
                    <p><?= $texts['registration_info_text'] ?></p>
                <?php endif; ?>
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
                    <div class="column margin-small-top">
                        <a href="javascript:;" onclick="Simpleshop.toggleAuth(this, '.login-form')" class="text-link">###action.back_to_login###</a>.
                    </div>
                <?php endif; ?>

            </div>

        </div>
    <?php endif; ?>
</div>
